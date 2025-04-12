<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class OrderController extends Controller
{

    public function getAllOrder()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
                       ->with(['orderDetails.product:id,productName,thumbnail', 'store:id,storeName'])
                       ->orderBy('created_at', 'desc')
                       ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders
        ], 200);
    }


    public function displayOrder($order_id)
    {
        $user = Auth::user();
        $order = Order::with(['orderDetails.product', 'store'])->find($order_id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => "Order not found."
            ], 404);
        }

        if ($order->user_id != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to access this order.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'order' => $order
        ], 200);
    }


     public function getOrdersByStatus($status)
     {
         $user = Auth::user();
         $validStatuses = ['Order Placed', 'Pending Payment', 'Paid', 'Processing', 'Out for Delivery', 'Delivered', 'Canceled', 'Payment Failed'];

         if (!in_array($status, $validStatuses)) {
             return response()->json([
                 'success' => false,
                 'message' => 'Invalid order status provided.'
             ], 400);
         }

         $orders = Order::where('user_id', $user->id)
                        ->where('status', $status)
                        ->with(['orderDetails.product:id,productName,thumbnail', 'store:id,storeName'])
                        ->orderBy('created_at', 'desc')
                        ->get();

         return response()->json([
             'success' => true,
             'orders' => $orders
         ], 200);
     }



    public function cancelOrderByUser($order_id)
    {
        $user = Auth::user();
        $order = Order::with('orderDetails')->find($order_id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => "Order not found."], 404);
        }
        if ($order->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to cancel this order.'], 403);
        }


        $allowedCancelStatuses = ['Order Placed', 'Pending Payment'];
        if (!in_array($order->status, $allowedCancelStatuses)) {
            return response()->json(['success' => false, 'message' => "Order cannot be canceled at its current status ({$order->status})."], 400);
        }


        DB::beginTransaction();
        try {
            $order->status = 'Canceled';
            $order->save();


            foreach ($order->orderDetails as $detail) {
                 Product::where('id', $detail->product_id)->increment('remainQuantity', $detail->quantity);
                 Product::where('id', $detail->product_id)->where('soldQuantity', '>=', $detail->quantity)->decrement('soldQuantity', $detail->quantity);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => "Order canceled successfully."], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Order Cancellation Error (User): " . $e->getMessage());
            return response()->json(['success' => false, 'message' => "Error canceling order."], 500);
        }
    }


    public function createOrderFromCart(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|array',
            'shipping_address.firstName' => 'required|string|max:255',
            'shipping_address.lastName' => 'required|string|max:255',
            'shipping_address.email' => 'required|email|max:255',
            'shipping_address.street' => 'required|string|max:255',
            'shipping_address.city' => 'required|string|max:255',
            'shipping_address.state' => 'required|string|max:255',
            'shipping_address.country' => 'required|string|max:255',
            'shipping_address.zipcode' => 'required|string|max:20',
            'selectedItems' => 'required|array|min:1',
            'selectedItems.*.product_id' => 'required|exists:product,id', // <-- ĐÃ SỬA THÀNH 'product'
            'selectedItems.*.quantity' => 'required|integer|min:1',
            'selectedItems.*.store_id' => 'required|exists:store,id',
            'amount' => 'required|numeric|min:0',
            'store_id' => 'required|exists:store,id',
            'paymentMethod' => ['required', Rule::in(['COD', 'BANKING'])],
            'phoneNumber' => 'required|string|max:20',
            'note' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $storeId = $request->store_id;
            $orderItems = $request->selectedItems;
            $totalAmount = 0;
            $validItems = [];


            foreach ($orderItems as $item) {
                 $product = Product::find($item['product_id']);
                 if (!$product) {
                     throw new \Exception("Product ID {$item['product_id']} not found.");
                 }
                 if ($product->store_id != $storeId) {
                      throw new \Exception("Product {$product->productName} does not belong to the specified store.");
                 }
                 if ($product->remainQuantity < $item['quantity']) {
                     throw new \Exception("Product {$product->productName} is out of stock for the requested quantity ({$item['quantity']}). Only {$product->remainQuantity} left.");
                 }
                 $totalAmount += $product->price * $item['quantity'];
                 $validItems[] = [
                     'product_id' => $product->id,
                     'quantity' => $item['quantity'],
                     'price' => $product->price
                 ];
            }


             $deliveryCharges = (float)env('DELIVERY_CHARGES', 0);
             $expectedTotal = $totalAmount + $deliveryCharges;


             if (abs($expectedTotal - (float)$request->amount) > 0.01) {
                 Log::warning("Order Amount Mismatch: Calculated {$expectedTotal}, Request {$request->amount}. Using calculated amount.");
             }


            $order = new Order();
            $order->user_id = $user->id;
            $order->store_id = $storeId;
            $order->order_date = now();
            $order->total_amount = $expectedTotal;
            $order->shipping_address = json_encode($request->shipping_address);
            $order->phone_number = $request->phoneNumber;
            $order->note = $request->note;
            $order->status = $request->paymentMethod === 'COD' ? 'Order Placed' : 'Pending Payment';
            $order->payment_method = $request->paymentMethod;
            $order->payment_status = false;
            $order->save();


            foreach ($validItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
                 Product::where('id', $item['product_id'])->decrement('remainQuantity', $item['quantity']);
                 Product::where('id', $item['product_id'])->increment('soldQuantity', $item['quantity']);
            }


             Cart::where('user_id', $user->id)->whereIn('product_id', array_column($validItems, 'product_id'))->delete();

            if ($request->paymentMethod === 'COD') {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully via COD!',
                    'order_id' => $order->id
                ], 201);

            } elseif ($request->paymentMethod === 'BANKING') {
                $vnp_Url = $this->generateVnpayUrl($order->id, $order->total_amount, $request->ip() ?? '127.0.0.1');

                if (!$vnp_Url) {
                     throw new \Exception("Failed to create VNPay payment URL.");
                }
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Order created. Redirecting to VNPay...',
                    'order_id' => $order->id,
                    'redirectUrl' => $vnp_Url
                ], 200);
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Order Creation Error: " . $e->getMessage(), ['request' => $request->all()]);

             $errorMessage = Str::contains($e->getMessage(), ['out of stock', 'not found', 'does not belong'])
                ? $e->getMessage()
                : 'Failed to place order. Please try again.';
            return response()->json(['success' => false, 'message' => $errorMessage], $e instanceof \Illuminate\Validation\ValidationException ? 422 : 500);
        }
    }


    protected function generateVnpayUrl($orderId, $amount, $ipAddress)
    {
        $vnp_TmnCode = env('VNPAY_TMNCODE');
        $vnp_HashSecret = env('VNPAY_HASHSECRET');
        $vnp_Url = env('VNP_URL', "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html");
        $vnp_Returnurl = env('VNP_RETURN_URL');

         if (!$vnp_TmnCode || !$vnp_HashSecret || !$vnp_Returnurl) {
             Log::error('VNPay environment variables are missing.');
             return null;
         }

        $vnp_TxnRef = $orderId . '_' . time();
        $vnp_OrderInfo = 'Thanh toan don hang #' . $orderId;
        $vnp_OrderType = 'other';
        $vnp_Amount = (int)($amount * 100);
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = $ipAddress;
        $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes'));

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate,
        ];

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $hashdata = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnp_SecureHash;

        Log::info("VNPay URL Generated for Order $orderId: $vnp_Url");
        return $vnp_Url;
    }


    public function handleVnpayReturn(Request $request)
    {
        Log::info('VNPay Return Data:', $request->all());
        $vnp_HashSecret = env('VNPAY_HASHSECRET');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

        if (!$vnp_HashSecret) {
             Log::error('VNPay Hash Secret is missing in environment.');
             return redirect($frontendUrl . '/order-failed?reason=config_error');
        }
        if ($vnp_SecureHash === null) {
             Log::warning('VNPay Return: Missing vnp_SecureHash');
             return redirect($frontendUrl . '/order-failed?reason=missing_hash');
        }

        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
             if ($i == 1) {
                 $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
             } else {
                 $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                 $i = 1;
             }
         }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $vnp_ResponseCode = $request->input('vnp_ResponseCode');
        $vnp_TxnRef = $request->input('vnp_TxnRef');
        $orderIdParts = explode('_', $vnp_TxnRef);
        $orderId = $orderIdParts[0] ?? null;


        Log::info("VNPay Return - Order ID: $orderId, Response Code: $vnp_ResponseCode, Hash Check: " . (hash_equals($secureHash, $vnp_SecureHash) ? 'Valid' : 'Invalid'));
        Log::info("Received Hash: " . $vnp_SecureHash);
        Log::info("Generated Hash: " . $secureHash);
        Log::info("Hash Data String: " . $hashData);


        if (hash_equals($secureHash, $vnp_SecureHash)) {
            $order = Order::find($orderId);

            if ($order) {
                 if ($order->payment_status == true) {
                      Log::warning("VNPay Return: Order ID $orderId already processed.");
                      return redirect($frontendUrl . '/order-success?orderId=' . $order->id . '&reason=already_processed');
                  }

                 if ($vnp_ResponseCode == '00') {
                    $order->payment_status = true;
                    $order->status = 'Paid';
                    $order->save();
                    Log::info("VNPay Return: Order ID $orderId payment successful.");
                    return redirect($frontendUrl . '/order-success?orderId=' . $order->id);
                } else {
                    $order->status = 'Payment Failed';
                    $order->save();
                    Log::warning("VNPay Return: Order ID $orderId payment failed/cancelled (Code: $vnp_ResponseCode).");
                    return redirect($frontendUrl . '/order-failed?orderId=' . $order->id . '&reason=vnpay_fail&code=' . $vnp_ResponseCode);
                }
            } else {
                Log::error("VNPay Return: Order ID $orderId not found.");
                return redirect($frontendUrl . '/order-failed?reason=order_not_found');
            }
        } else {
            Log::error("VNPay Return: Invalid signature for Order ID potentially $orderId.");
             $order = Order::find($orderId);
             if ($order && $order->payment_status == false) {
                $order->status = 'Payment Failed';
                $order->save();
             }
            return redirect($frontendUrl . '/order-failed?reason=invalid_signature' . ($orderId ? '&orderId=' . $orderId : ''));
        }
    }

}