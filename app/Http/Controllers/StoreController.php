<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Store;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Services\CloudinaryAdapter;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    protected $cloudinary;

    public function __construct(CloudinaryAdapter $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function getProductsList (Request $request)
    {
        if (!$request->store) { return response()->json(['success' => false, 'message' => 'Store context not found.'], 403); }
        $products = Product::with('imageDetails')
                           ->where("store_id", $request->store->id)
                           ->orderBy('created_at', 'desc')
                           ->get();
        return response()->json(['success' => true, 'products' => $products], 200);
    }

    public function getOrderList(Request $request)
    {
         if (!$request->store) {
            return response()->json(['success' => false, 'message' => 'Store context not found.'], 403);
         }
        $orders = Order::with([
                'orderDetails.product:id,productName,thumbnail',
                'user:id,email,firstName,lastName'
            ])
            ->where("store_id", $request->store->id)
            ->orderBy('created_at', 'desc')
            ->get();

         // Bỏ json_decode nếu model Order đã dùng $casts['shipping_address' => 'array']
         /*
         $orders->each(function ($order) {
             if (is_string($order->shipping_address)) {
                  try {
                     $order->shipping_address = json_decode($order->shipping_address, false, 512, JSON_THROW_ON_ERROR);
                  } catch (\JsonException $e) {
                     Log::error("Failed to decode shipping_address for order ID {$order->id}: " . $e->getMessage());
                     $order->shipping_address = null;
                  }
             }
         });
         */

        return response()->json(['success' => true, 'orders' => $orders], 200);
    }

    public function index()
    {
        $store = Store::where('status', 'approved')->get();
        return response()->json(['success' => true, 'stores' => $store], 200); // Trả về success: true
    }

    public function create(Request $request){
        $user = Auth::user();

        if ($user->store()->whereIn('status', ['approved', 'pending'])->exists()) {
             return response()->json(['success' => false, 'message' => 'You already have an active or pending store request.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'storeName'   => 'required|max:255|unique:store,storeName',
            'description' => 'required|string|max:1000',
            'avatar'      => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
             return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $uploadResult = $this->cloudinary->upload( $request->file('avatar'), ['folder' => 'stores', 'public_id' => time() . '_' . pathinfo($request->file('avatar')->getClientOriginalName(), PATHINFO_FILENAME) ]);

            $store = new Store;
            $store->ownId = $user->id;
            $store->storeName = $request->storeName;
            $store->description = $request->description;
            $store->avatar = $uploadResult['secure_url'];
            $store->status = 'pending';
            $store->save();

             if (method_exists($user, 'refresh')) { $user->refresh(); }

            return response()->json(['success' => true, 'message' => 'Store registration request submitted successfully. Please wait for approval.', 'store' => $store ], 201);

        } catch (\Exception $e) {
             Log::error("Store Creation Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error processing request: ' . $e->getMessage()], 500);
        }
    }

    public function update_profile(Request $request)
    {
        $user = Auth::user();
        if (!$user) { return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401); }

        $store = $request->store;
        if (!$store) { return response()->json(['success' => false, 'message' => "Store not found for this user."], 404); }

        $validator = Validator::make($request->all(), [
            'storeName'   => ['sometimes', 'required', 'max:255', Rule::unique('store', 'storeName')->ignore($store->id)],
            'description' => 'sometimes|required|string|max:1000',
            'avatar'      => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
        ]);

        if ($validator->fails()){ return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422); }

        if ($request->filled('storeName')) { $store->storeName = $request->storeName; }
        if ($request->filled('description')) { $store->description = $request->description; }

        if ($request->hasFile('avatar')) {
            try {
                $uploadResult = $this->cloudinary->upload( $request->file('avatar'), ['folder' => 'stores', 'public_id' => time() . '_' . pathinfo($request->file('avatar')->getClientOriginalName(), PATHINFO_FILENAME), 'overwrite' => true ]);
                $store->avatar = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                Log::error('Avatar Upload Error: '. $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Error uploading image: ' . $e->getMessage()], 500);
            }
        }

        $store->save();
        $store->refresh();

        return response()->json(["success"  => true, "message" => "Successfully updated store profile", "store" => $store ], 200);
    }

    public function findStoreById(int $store_id)
    {
        $store = Store::with('owner:id,firstName,lastName,email')->find($store_id);
        if (!$store) { return response()->json(["success" => false, "message" =>"Store not found"], 404); }
        return response()->json(["success"=> true, "store" => $store]);
    }

    public function findStoreByOwnId(int $user_id)
    {
        $store = Store::where('ownId', $user_id)->first();
        if (!$store) { return response()->json(["success" => false, "message" =>"Store not found"], 404); }
        return response()->json(["success"=> true, "store" => $store]);
    }

    public function deleteStore(Request $request)
    {
        $user = Auth::user();
        if (!$user) { return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401); }

        $store = $request->store;
        if (!$store) { return response()->json(["success" => false, "message" => "Store not found"], 404); }

        $store->delete();
        return response()->json(["success"  => true, "message" => "Store deleted successfully"], 200);
    }

    public function myStore(Request $request)
    {
        $user = Auth::user();
        if (!$user) { return response()->json(["success"=> false, "message"=> "User not authenticated."], 401); }

        $store = Store::where("ownId", $user->id)->first();
        if (!$store) { return response()->json(["success"=> false, "message"=> "Store not found"], 404); }

        $store->load('owner:id,firstName,lastName,email');
        return response()->json(["success"=> true, "store" => $store ]);
    }

    public function findStoreByStoreName($storeName)
    {
        $stores = Store::where('storeName', 'LIKE', '%' . $storeName . '%')
                       ->where('status', 'approved')
                       ->with('owner:id,firstName,lastName,email')
                       ->get();
        if ($stores->isEmpty()) { return response()->json(["success" => false, "message" => "No approved stores found"], 404); }
        return response()->json(["success" => true, "stores" => $stores], 200);
    }

    public function updateOrderStatus(Request $request, $order_id)
    {
        $order = Order::find($order_id);
        if (!$order) { return response()->json(["success" => false, "message" => "Order not found"], 404); }
        if($order->store_id != $request->store->id){ return response()->json(["success" => false, "message" => "Unauthorized"], 403); }

        $validator = Validator::make($request->all(), [
             'status' => ['required', Rule::in(['Processing', 'Out for Delivery', 'Delivered', 'Canceled'])]
        ]);
        if ($validator->fails()) { return response()->json(['success' => false, 'message' => 'Invalid status provided.', 'errors' => $validator->errors()], 400); }

        $currentStatus = $order->shipping_status;
        $newStatus = $request->input('status');

        $allowedUpdateFrom = ['Waiting for Pickup', 'Paid', 'Processing', 'Out for Delivery'];
         if (!in_array($currentStatus, $allowedUpdateFrom)) {
             return response()->json(['success' => false, 'message' => "Order status cannot be updated from its current state ({$currentStatus})."], 400);
         }

        DB::beginTransaction();
        try {
             $order->shipping_status = $newStatus;
             $order->save();

             if ($newStatus === 'Canceled' && $currentStatus !== 'Canceled') {
                  foreach ($order->orderDetails()->get() as $detail) {
                      Product::where('id', $detail->product_id)->increment('remainQuantity', $detail->quantity);
                      Product::where('id', $detail->product_id)->where('soldQuantity', '>=', $detail->quantity)->decrement('soldQuantity', $detail->quantity);
                  }
             }
             DB::commit();
             return response()->json(["success" => true, "message" => "Order status updated to {$newStatus}"], 200);
         } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Store updating order status error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => "Error updating order status."], 500);
         }
    }

    public function cancelOrderByStore(Request $request, $order_id)
    {
        $order = Order::find($order_id);
        if (!$order) { return response()->json(['success' => false, 'message' => "Order not found"], 404); }
        if ($order->store_id != $request->store->id) { return response()->json(['success' => false, 'message' => 'Unauthorized'], 403); }

         $allowedCancelStatuses = ['Waiting for Pickup', 'Paid', 'Pending Payment'];
         if (!in_array($order->shipping_status, $allowedCancelStatuses)) {
             return response()->json(['success' => false, 'message' => "Order cannot be canceled at this stage."], 400);
         }

         $request->merge(['status' => 'Canceled']);
         return $this->updateOrderStatus($request, $order_id);
    }
}
