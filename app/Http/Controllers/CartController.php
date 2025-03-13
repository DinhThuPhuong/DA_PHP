<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\CartItems;
use App\Models\Store;

use Illuminate\Http\Request;

class CartController extends Controller
{
    //Kiem tra cua hang 
    protected function checkStoreExists(int $store_id)
    {
        return Store::findOrFail($store_id);
    }

    // Kiem tra san pham 
    protected function checkProductExists(int $product_id)
    {
        return Product::findOrFail($product_id);
    }
    public function addToCart(Request $request, int $product_id)
    {
        $user = Auth::user(); // Kiem tra thong tin nguoi dung dang nhap

        // Kiem tra san pham va cua hang co ton tai khong
        $product = $this->checkProductExists($product_id); 

        $store_id = $product->store_id; // Neu san pham ton tai thi lay storeId tu san pham

        // So luong san pham can them vao gio hang, neu khong co du lieu thi mac dinh la 1
        $quantity = $request->input('quantity', 1);

        // Truy van cartItem 
        $cartItem = CartItems::where('user_id', $user->id)
                            ->where('product_id', $product_id)
                            ->first();

        if ($cartItem) {
            // Neu sp da co trong gio hang
            $cartItem->update([
                'quantity' => $cartItem->quantity + $quantity,
                'store_id' => $store_id
            ]);
        } else {
            // Neu chua co thi tao moi
            $cartItem = CartItems::create([
                'user_id' => $user->id,
                'product_id' => $product_id,
                'quantity' => $quantity,
                'store_id' => $store_id
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Product added to cart successfully',
            'cart_item' => $cartItem
        ], 200);
    }

    //So luong san pham co trong gio hang
    public function count()
    {
        $user = Auth::user();
        $cartCount = CartItems::where('user_id', $user->id)->count();
        return response()->json([
            'status' => 200,
            'cart_count' => $cartCount
        ], 200);
    }

    //Xem gio hanghang
    public function viewCart()
    {
        $cartItems = CartItems::with(['product', 'store'])
            ->where('user_id', Auth::id())
            ->get()
            ->groupBy('store_id'); // Nhom san pham theo store

        return response()->json([
            'status' => 200,
            'cart' => $cartItems
        ]);
    }

    //Cap nhat so luong 
    public function updateCart(Request $request, int $product_id)
    {
        $user = Auth::user(); // Lay thong tin cua nguoi dung dang nhap
    
        // Kiem tra san pham ton tai
        $product = $this->checkProductExists($product_id);
    
        // Kiem tra san pham co ton tai trong gio hang khong
        $cartItem = $this->getCartItem($user->id, $product_id);
    
        // Cap nhat lai so luong trong gio hang neu co
        $cartItem->quantity = $request->input('quantity', $cartItem->quantity); 

        //Luu du lieu 
        $cartItem->save(); // 
    
        
        return response()->json([
            'status' => 200,
            'message' => 'Cart updated successfully',
            'cart_item' => $cartItem
        ], 200);
    }
    
    protected function getCartItem(int $userId, int $productId)
    {
        $cartItem = CartItems::where('user_id', $userId)
                             ->where('product_id', $productId)
                             ->first();
    
        // Neu khong tim thay tra ve loi 404
        if (!$cartItem) {
            abort(404, 'Product not found in cart');
        }
    
        return $cartItem;
    }
    

    //Xoa 1 san pham ra khoi cart  
    public function removeFromCart(int $product_id)
    {
        $user = Auth::user();

        //Tra ve thong tin item hoac loi 404
        $cartItem = $this->getCartItem($user->id, $product_id);
        
        //Thuc hien xoa neu khong co loi
        $cartItem->delete();

        return response()->json([
            'status'=> 200,
            'message'=> 'Product removed succesfully'
            ],200);
    }

    //Chuc nang xoa toan bo gio hang

    public function clearCart()
{   
    $user = Auth::user();

    //Xoa toan bo san pham co trong gio hang
    CartItems::where('user_id', $user->id)->delete();

    return response()->json([
        'status'  => 200,
        'message' => 'All products removed from cart successfully!'
    ], 200);
}


    



}