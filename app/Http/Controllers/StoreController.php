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
        $products = Product::with('imageDetails') // <-- THÊM .with('imageDetails') VÀO ĐÂY
                            ->where("store_id", $request->store->id)
                            ->orderBy('created_at', 'desc') // Thêm sắp xếp nếu muốn
                            ->get();
        // Dữ liệu trả về giờ sẽ bao gồm mảng 'image_details' cho mỗi sản phẩm
        return response()->json($products, 200);
    }


    public function getOrderList(Request $request)
    {
        $orders = Order::with('orderDetails.product') // Load cả product trong order detail
            ->where("store_id", $request->store->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders, 200);
    }


    public function index()
    {
        $store = Store::where('status', 'approved')->get(); // Chỉ lấy store đã duyệt

        $data = [
            'status' => 200,
            'store' => $store
        ];
        return response()->json($data, 200);

    }


    public function create(Request $request){
        $user = Auth::user();


        if ($user->store()->whereIn('status', ['approved', 'pending'])->exists()) {
             return response()->json([
                 'status' => 403,
                 'message' => 'You already have an active or pending store request.'
             ], 403);
        }



        $validator = Validator::make($request->all(), [
            'storeName'   => 'required|max:255|unique:store,storeName',
            'description' => 'required',
            'avatar'      => 'required|image|mimes:jpg,jpeg,png,gif,svg|max:2048'
        ]);


        if ($validator->fails())
        {
            $data = [
                'status' => 422,
                'message' => $validator->messages()
            ];
            return response()->json($data,422);
        }

        try {

            $uploadResult = $this->cloudinary->upload(
                $request->file('avatar'),
                [
                    'folder' => 'stores',
                    'public_id' => time() . '_' . pathinfo($request->file('avatar')->getClientOriginalName(), PATHINFO_FILENAME),
                ]
            );


            $store = new Store;
            $store->ownId = $user->id;
            $store->storeName = $request->storeName;
            $store->description = $request->description;
            $store->avatar = $uploadResult['secure_url'];


            $store->save();


             if (method_exists($user, 'refresh')) { // Optional: Refresh user relation if needed by other logic
                 $user->refresh();
             }

            $data = [
                'status' => 201,
                'message' => 'Store registration request submitted successfully. Please wait for approval.',
                'store' => $store
            ];


            return response()->json($data, 201);

        } catch (\Exception $e) {
             Log::error("Store Creation Error: " . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Error processing request: ' . $e->getMessage()
            ], 500);
        }
    }


    public function update_profile(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated.',
            ], 401);
        }


        $store1 = $request->store;
        if (!$store1) {
             return response()->json([
                'status' => 404,
                'message' => "Store not found for this user."
            ], 404);
        }


        $validator = Validator::make($request->all(), [
            'storeName'   => [
                'sometimes', // only validate if present
                'required',
                'max:255',
                Rule::unique('store', 'storeName')->ignore($store1->id)
            ],
            'description' => 'sometimes|required', // only validate if present
            'avatar'      => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
        ]);

        if ($validator->fails()){
            return response()->json([
                'status'  => 422,
                'message' => $validator->errors() // Trả về lỗi validation chi tiết
            ], 422);
        }


        if ($request->filled('storeName')) { // Sử dụng filled() để kiểm tra có và không rỗng
            $store1->storeName = $request->storeName;
        }
        if ($request->filled('description')) {
            $store1->description = $request->description;
        }


        if ($request->hasFile('avatar')) {
            try {
                $uploadResult = $this->cloudinary->upload(
                    $request->file('avatar'),
                    [
                        'folder' => 'stores',
                        'public_id' => time() . '_' . pathinfo($request->file('avatar')->getClientOriginalName(), PATHINFO_FILENAME),
                        'overwrite' => true
                    ]
                );

                $store1->avatar = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                Log::error('Avatar Upload Error: '. $e->getMessage());
                return response()->json([
                    'status' => 500,
                    'message' => 'Error uploading image: ' . $e->getMessage()
                ], 500);
            }
        }

        $store1->save();

         // Refresh model data before returning
         $store1->refresh();

        return response()->json([
            "status"  => 200,
            "message" => "Successfully updated store profile",
            "store"   => $store1
        ], 200);
    }


    public function findStoreById(int $store_id)
    {
        $store = Store::with('owner:id,firstName,lastName,email')->find($store_id); // Eager load owner info
        if (!$store) {
            return response()->json([
                "status" => 404,
                "message" =>"Store with id = $store_id not found"
            ],404);
        }

        return  response()->json([
            "status"=> 200,
            "store" => $store
        ]);

    }


    public function findStoreByOwnId(int $user_id)
    {
        $store = Store::where('ownId', $user_id)->first();
        if (!$store) {
            return response()->json([
                "status" => 404,
                "message" =>"Store with ownId = $user_id not found"
            ],404);
        }

        return response()->json([
            "status"=> 200,
            "store" => $store
        ]);

    }


    public function deleteStore(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated.',
            ], 401);
        }
        $store = $request->store; // Store is attached by CheckStoreAuth middleware

        if (!$store) {
            return response()->json([
                "status" => 404,
                "message" => "Store not found"
            ], 404);
        }


        $store->delete();

        return response()->json([
            "status"  => 200,
            "message" => "Store deleted successfully"
        ], 200);
    }


    public function myStore(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                "status"=> 401,
                "message"=> "User not authenticated."
                ], 401);
        }


        $store = Store::where("ownId", $user->id)->first();

        if (!$store) {
            return response()->json([
                "status"=> 404,
                "message"=> "Store not found"
                ], 404);
        }


        $store->load('owner:id,firstName,lastName,email'); // Tùy chọn load owner info

        return response()->json([
            "status"=> 200,
            "store" => $store
            ]);

    }


    public function findStoreByStoreName($storeName)
    {

        $stores = Store::where('storeName', 'LIKE', '%' . $storeName . '%')
                       ->where('status', 'approved')
                       ->with('owner:id,firstName,lastName,email')
                       ->get();

        if ($stores->isEmpty()) {
            return response()->json([
                "status" => 404,
                "message" => "No approved stores found with name containing '$storeName'"
            ], 404);
        }

        return response()->json([
            "status" => 200,
            "stores" => $stores
        ], 200);
    }


    public function updateOrderStatus(Request $request, $order_id)
    {

        $order = Order::find($order_id);
        if (!$order) {
            return response()->json([
                "status" => 404,
                "message" => "Order not found"
            ], 404);
        }


        if($order->store_id != $request->store->id){
            return response()->json([
                "status" => 403,
                "message" => "Unauthorized to update this order"
            ], 403);
        }


        if($order->shipping_status != 'Waiting for Pickup'){
            return response()->json([
                "status" => 400,
                'message' => "Order with id = $order_id cannot be updated"
            ], 400);
        }


        $order->shipping_status = "In Delivery";
        $order->save();

        return response()->json([
            "status" => 200,
            "message" => "Order status updated to In Delivery"
        ], 200);
    }


    public function cancelOrderByStore(Request $request, $order_id)
    {


        $order = Order::find($order_id);
        if (!$order) {
            return response()->json([
                'status' => 404,
                'message' => "Order with id = $order_id not found"
            ], 404);
        }


        if ($order->store_id != $request->store->id) {
            return response()->json([
                'status' => 403,
                'message' => 'User is not authorized to cancel this order'
            ], 403);
        }


        if ($order->shipping_status != 'Waiting for Pickup') {
            return response()->json([
                'status' => 400,
                'message' => "Order with id = $order_id cannot be canceled"
            ], 400);
        }


        $order->shipping_status = 'Canceled';
        $order->save();


         // Optionally: Restore product quantities, handle refunds etc.

        return response()->json([
            'status' => 200,
            'message' => "Order with id = $order_id has been canceled successfully"
        ], 200);
    }
}   