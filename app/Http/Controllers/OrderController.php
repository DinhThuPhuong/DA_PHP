<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\CartItems;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;


class OrderController extends Controller
{

    //Truy van danh sach order trong csdl (Chuc nang cua nguoi dung)
    public function getAllOrder()
    {
        //Truy van thong tin nguoi dung dang dang nhap
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)  // Tim don hang theo user_id
                        ->with(['orderDetails.product'])  // Lay chi tiet don hang va san pham
                        // ->orderBy('created_at', 'desc')  // Sap xep don hang  theo thoi gian moi nhat
                        ->get();

        return response()->json($orders,200);

    }

    //Truy van chi tiet order theo id (Chuc nang cua nguoi dung)
    public function displayOrder( $order_id)
    {
        //Kiem tra trang thai dang nhap cua nguoi dung
        $user = Auth::user();
        //Truy van don hang cung voi chi tie don hang theo order_id
        $order = Order::with('orderDetails')->find($order_id);
        if(!$order)
        {
            return response()->json([
                'status'=> 404,
                'message'=> "Order with id = $order_id not found"
                ],404);
        }

        //Kiem tra xem don hang co thuoc voi nguoi dung hien tai hay khong
        if($order->user_id != $user->id)
        {
            return response()->json([
                'status'=> 403,
                'message'=> 'User is not authorized to access this order'
                ],403);
        }
        return response()->json($order,200);
    }
    

    // //Nguoi dung thuc hien xoa don hang theo id (Chua hoan thien)

    // public function deleteOrderByUser($order_id)
    // {
    //     //Truy van don hang cung voi chi tiet don hang theo order_id
    //     $order = Order::with('orderDetails')->find($order_id);
    //     if(!$order) {
    //         return response()->json([
    //             'status'=> 404, 
    //             'message'=> "Order with id = $order_id not found"
    //         ], 404);
    //     }

    //     //Kiem tra xem don hang co thuoc voi nguoi dung hien tai hay khong  
    //     if($order->user_id != Auth::id()) {
    //         return response()->json([
    //             'status'=> 403,
    //             'message'=> 'User is not authorized to delete this order'
    //         ], 403);
    //     }

    //     //Kiem tra xem don hang co dang o trang thai waiting for pickup hay khong
    //     if ($order->shipping_status != 'Waiting for Pickup') {
    //         return response()->json([
    //             'status' => 400,
    //             'message' => "Order with id = $order_id cannot be deleted"
    //         ], 400);
    //     }

    //     try {
    //         DB::beginTransaction();
    //         //Thuc hien cap nhat so luong san pham con lai va so luong san pham da ban
    //         foreach ($order->orderDetails as $orderDetail) {
    //             $product = Product::find($orderDetail->product_id);
    //             if ($product) {
    //                 //Thuc hien cap nhat so luong san pham con lai va so luong san pham da ban
    //                 $product->remainQuantity += $orderDetail->quantity;
    //                 $product->soldQuantity -= $orderDetail->quantity;
    //                 $product->save();
    //             }
    //         }

    //         $order->delete();
    //         DB::commit();

    //         return response()->json([
    //             'status' => 200,
    //             'message' => "Order with id = $order_id deleted successfully"
    //         ], 200);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => 500,
    //             'message' => "Error deleting order: " . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function createOrderFromCart(Request $request)
    {
        // Lay thong tin nguoi dung dang dang nhap
        $user = Auth::user();
        
        // Ham kiem tra du lieu dau vao
        $validation = $this->validateOrderRequest($request);
        if ($validation) {
            return $validation;
        }

        DB::beginTransaction();

        try {
            $totalPrice = 0;
            $groupedItems = $this->groupItemsByStore($request->selectedItems);

            // Tao don hang cho moi cua hang
            foreach ($groupedItems as $storeId => $items) {
                $order = $this->createOrderForStore($storeId, $user, $request, $items);

                $totalPriceForStore = 0;
                foreach ($items as $item) {
                    // Tao orderDetail cho tung san pham va tinh tong gia tri cua don hang
                    $price = $this->createOrderDetailForProduct($item, $order);
                    $totalPriceForStore += $price;
                }
                Log::info("Total price 1: " . $totalPriceForStore);
                // Cap nhat tong gia tri cua don hang
                $order->totalPrice = $totalPriceForStore;
                Log::info("Total price 2: " . $order->totalPrice);
                

                $order->save();

                // Xoa san pham ra khoi gio hang
                $this->deleteCartItems($user->id, $items);
            }

            if ($request->paymentMethod === 'BANKING') {
                DB::commit(); // commit trước khi redirect
                $redirectUrl = $this->redirectToVnpay($order);
                return redirect($redirectUrl);
            }

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Order placed successfully from cart!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();  // Thuc hien rollback neu co loi
            return response()->json([
                'status' => 500,
                'message' => 'Error creating order: ' . $e->getMessage()
            ], 500);
        }
    }

    //Nguoi dung thuc hien mua truc tiep 
    public function createDirectOrder(Request $request)
    {
        // Kiem tra nguoi dung dang nhap hien tai
        $user = Auth::user();
        
        // Ham kiem tra du lieu dau vao
        $validation = Validator::make($request->all(), [
            'product_id' => 'required|exists:product,id',
            'quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
            'note' => 'nullable|string',
            'phoneNumber' => 'required|string',
            'paymentMethod' => 'required|in:COD,BANKING'
        ]);
        //Neu du lieu khong hop le
        if ($validation->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validation->errors()->first()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $totalPrice = 0;

            // Mua truc tiep
            $item = [
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'store_id' => Product::find($request->product_id)->store_id // Lay store_id tu san pham
            ];

            // Tao don hang cho store
            $order = $this->createOrderForStore($item['store_id'], $user, $request, [$item]);

            // Tao chi tiet don hang cho san pham
            $price = $this->createOrderDetailForProduct($item, $order);
            $totalPrice += $price;

            // Cap nhat tong gia tri cua don hang
            $order->totalPrice = $totalPrice;
            Log::info("Total price 1: " . $totalPrice);
            Log::info("Total price 2: " . $order->totalPrice);
            $order->save();
            if ($request->paymentMethod === 'BANKING') {
                DB::commit(); // commit trước khi redirect
                $redirectUrl = $this->redirectToVnpay($order);
                return redirect($redirectUrl);
            }

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Order placed successfully from direct purchase!',
                'totalPrice' => $totalPrice
            ]);

        } catch (\Exception $e) {
            DB::rollBack();  // Thuc hien rollback neu co loi
            return response()->json([
                'status' => 500,
                'message' => 'Error creating order: ' . $e->getMessage()
            ], 500);
        }
    }

    
    // VNPAY_TMNCODE=4NILMY5M
    // VNPAY_HASHSECRET=WKCLSZQR1ZVS4I6W1BW4XBT45I01NZEJ
    // VNP_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
    // VNP_RETURN_URL=http://localhost:8000/api/vnpay/return

    private function redirectToVnpay($order)
    {
        $vnp_TmnCode = "4NILMY5M";
        $vnp_HashSecret = "WKCLSZQR1ZVS4I6W1BW4XBT45I01NZEJ";
        //$vnp_Url = env('VNP_URL');
        $vnp_Returnurl = "http://localhost:8000/api/vnpay/return";

        $vnp_TxnRef = $order->id; // Mã giao dịch (duy nhất)
        $vnp_OrderInfo = 'Thanh toan don hang #' . $order->id;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $order->totalPrice * 100; // VNPay yêu cầu nhân 100
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();

        // Nếu bạn muốn gửi kèm bank_code từ frontend, truyền request vào hàm này
        $vnp_BankCode = request()->input('bank_code', ''); // Lấy bank_code nếu có

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
            "vnp_TxnRef" => $vnp_TxnRef
        ];

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        // Sắp xếp dữ liệu để tạo hash
       
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Url .= "?" . $query;
        
        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnp_SecureHash;
        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        Log::info("Generated Secure Hash: " . $secureHash);
        Log::info("VNPay Received Secure Hash: " . $vnp_SecureHash);
        Log::info("VNPay URL: " . $vnp_Url);
        return $vnp_Url;
    }


    

    //Handle VNPay response
    public function handleVnpayReturn(Request $request)
    {
        $vnp_HashSecret = "WKCLSZQR1ZVS4I6W1BW4XBT45I01NZEJ"; 
        $inputData = $request->all();
    
        if (!isset($inputData['vnp_SecureHash'])) {
            return response()->json([
                'status' => 400,
                'message' => 'Missing secure hash'
            ]);
        }
    
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);
    
        ksort($inputData);
    
        $hashDataArr = [];
        foreach ($inputData as $key => $value) {
            $hashDataArr[] = $key . '=' . urlencode($value); // urlencode từng giá trị!
        }
        $hashData = implode('&', $hashDataArr);
    
        Log::info("VNPay Hash Data: " . $hashData);
    
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        Log::info("Generated Secure Hash: " . $secureHash);
        Log::info("VNPay Received Secure Hash: " . $vnp_SecureHash);
        $orderId = $request->vnp_TxnRef;
        $order = Order::find($orderId);
        if (hash_equals($secureHash, $vnp_SecureHash)) {
            
    
            if ($order && $request->vnp_ResponseCode == '00') {
                $order->payment_status = 'Paid';
                $order->save();
    
                return response()->json([
                    'status' => 200,
                    'message' => 'Payment successful',
                    'order_id' => $orderId
                ]);
            } else {
                $order->payment_status = 'Failed';
                $order->save();
                return response()->json([
                    'status' => 400,
                    'message' => 'Payment failed or order not found'
                ]);
            }
        } else {
            $order->payment_status = 'Failed';
            $order->save();
            return response()->json([
                'status' => 400,
                'message' => 'Invalid signature from VNPay'
            ]);
        }
    }
    
    


    //Nguoi dung thuc hien huy don hang theo id 
    public function cancelOrderByUser($order_id)
    {
        // Truy van don hang cung voi chi tiet don hang theo order_id
    $order = Order::with('orderDetails')->find($order_id);
    if (!$order) {
        return response()->json([
            'status' => 404,
            'message' => "Order with id = $order_id not found"
        ], 404);
    }

    // Kiem tra xem don hang co thuoc voi nguoi dung hien tai hay khong  
    if ($order->user_id != Auth::id()) {
        return response()->json([
            'status' => 403,
            'message' => 'User is not authorized to cancel this order'
        ], 403);
    }

    // Kiem tra xem don hang co dang o trang thai cho phep huy hay khong
    if ($order->shipping_status == 'Waiting for Pickup') {
        try {
            DB::beginTransaction();

            // Cap nhat trang thai don hang thanh "da huy"
            $order->shipping_status = 'Canceled'; 
            $order->save();

            // Cap nhat so luong san pham trong kho
            foreach ($order->orderDetails as $orderDetail) {
                $product = Product::find($orderDetail->product_id);
                if ($product) {
                    $product->remainQuantity += $orderDetail->quantity;
                    $product->soldQuantity -= $orderDetail->quantity;
                    $product->save();
                }
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => "Order with id = $order_id has been canceled successfully"
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => "Error canceling order: " . $e->getMessage()
            ], 500);
        }
    } else {
        return response()->json([
            'status' => 400,
            'message' => "Order with id = $order_id cannot be canceled"
        ], 400);
    }
}

public function getOrdersByStatus($status)
{
    // Truy van thong tin nguoi dung dang dang nhap
    $user = Auth::user();

    // Kiem tra trang thai hop le
    $validStatuses = ['Waiting for Pickup', 'In Delivery', 'Delivered', 'Canceled'];
    if (!in_array($status, $validStatuses)) {
        return response()->json([
            'status' => 400,
            'message' => 'Invalid order status'
        ], 400);
    }

    // Truy van don hang theo trang thai
    $orders = Order::where('user_id', $user->id)
                    ->where('shipping_status', $status)
                    ->with(['orderDetails.product']) // Lay chi tiet don hang va san pham
                    ->get();

    return response()->json($orders, 200);
}






    ////////Cac chuc nang ho tro create 1 Order ///////////

    // Kiem tra du lieu dau vao cho don hang mua tu cart
    private function validateOrderRequest($request)
    {
        $validator = Validator::make($request->all(), [
            'selectedItems' => 'required|array',
            'selectedItems.*.product_id' => 'required|exists:product,id',
            'selectedItems.*.quantity' => 'required|integer|min:1',
            'selectedItems.*.store_id' => 'required|exists:store,id', // store_id phai ton tai
            'shipping_address' => 'required|string',
            'note' => 'nullable|string',
            'phoneNumber' => 'required|string',
            'paymentMethod' => 'required|in:COD,BANKING'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ], 422);
        }
        return null;
         
    }

    
    // Gom nhom cac san pham theo store_id cho don hang mua tu cart
    private function groupItemsByStore($selectedItems)
    {
        $groupedItems = [];
        foreach ($selectedItems as $item) {
            $groupedItems[$item['store_id']][] = $item;  // Gom nhom theo store_id
        }
        return $groupedItems;
    }

     // Tao don hang cho tung cua hang (Cho don hang mua truc tiep va don hang mua tu cart)
     private function createOrderForStore($storeId, $user, $request, $items)
     {
         $order = new Order();
         $order->user_id = $user->id;
         $order->shipping_address = $request->shipping_address;
         $order->note = $request->note;
         $order->paymentMethod = $request->paymentMethod;
         $order->payment_status = 'Pending';
         //$order->payment_status = '$request->paymentMethod === 'COD' ? 'Pending' : 'Paid'';
         $order->shipping_status = 'Waiting for Pickup';
         $order->phoneNumber = $request->phoneNumber;
         $order->totalPrice = 0;  // Sẽ tính sau
         $order->store_id = $storeId;
         $order->created_at = now();
         $order->save();
 
         return $order;
     }

      // Tao chi tiet don hang cho tung san pham (Cho don hang mua truc tiep va don hang mua tu cart)
    private function createOrderDetailForProduct($item, $order)
    {
        $product = Product::find($item['product_id']);
    
        if (!$product) {
            throw new \Exception("Product does not exist");
        }
    
        if ($product->remainQuantity < $item['quantity']) {
            throw new \Exception("Product {$product->productName} is not enough in stock");
        }
    
        // Tao chi tiet don hang
        $orderDetail = new OrderDetail();
        $orderDetail->order_id = $order->id;
        $orderDetail->product_id = $item['product_id'];
        $orderDetail->quantity = $item['quantity'];
        $orderDetail->save();
    
        // Cap nhat so luong san pham
        $product->remainQuantity -= $item['quantity'];
        $product->soldQuantity += $item['quantity'];
        $product->save();
    
        return $product->price * $item['quantity'];
    }

    // Xoa san pham khoi gio hang (Cho don hang mua tu cart)
    private function deleteCartItems($userId, $items)
    {
        foreach ($items as $item) {
            CartItems::where('user_id', $userId)
                ->where('product_id', $item['product_id'])
                ->delete();
        }
    }
}


        
            




    