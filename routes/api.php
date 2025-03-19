<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\StoreNotificationController;


Route::middleware('auth:sanctum')->get('/user/profile', function( Request $request)
{
    return $request->user();
});

//CategoryController
Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::middleware('auth:sanctum','is-admin')->group(function () {
        Route::post('/create', [CategoryController::class, 'create']);
        Route::put('/update/{id}', [CategoryController::class, 'update']);
        Route::delete('/delete/{id}', [CategoryController::class,'delete']);
        
    });

    
  
});

//UserController
Route::prefix('user')->group(function () {
    // Cac chuc nang can xac thuc va co quyen admin
    Route::middleware('auth:sanctum','is-admin')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::delete('/deleteByAdmin/{id}', [UserController::class, 'deleteUserByAdmin']);
    });

    // Cac chuc nang  can xac thuc
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/update-profile', [UserController::class, 'updateProfile']);
        Route::delete('/delete', [UserController::class, 'deleteByUser']);
        Route::get('/getProfile', [UserController::class, 'getProfile']);
    });
});

//ProductController
Route::prefix('product')->group(function () {
    //Cac chuc nang khong can xac thuc
    Route::get('/', [ProductController::class, 'getAllProduct']);
    Route::get('/search/{keyword}', [ProductController::class, 'searchProduct']);
    Route::get('/display/{id}', [ProductController::class, 'display']);

    // Cac chuc nang can xac thuc va co quyen store
    Route::middleware('auth:sanctum','is-store')->group(function () {
      
        Route::post('/createProduct', [ProductController::class, 'createProduct']);
        Route::put('/update/{id}', [ProductController::class, 'updateProduct']);
        Route::delete('/delete/{id}', [ProductController::class, 'deleteProduct']);
       
    });

    // Cac chuc nang  can xac thuc
    // Route::middleware('auth:sanctum')->group(function () {
        
    
});



// RoleController
Route::prefix('role')->middleware('auth:sanctum','is-admin')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/create', [RoleController::class, 'create']);
    Route::put('/update/{id}', [RoleController::class, 'update']);
    Route::delete('/delete/{id}', [RoleController::class,'delete']);
  
});

//StoreController
Route::prefix('store')->group(function () {
    //Cac chuc nang khong can xac thuc
    Route::get('/', [StoreController::class, 'index']);
    Route::get('/findStoreById/{store_id}', [StoreController::class, 'findStoreById']);
   

    //Cac chuc nang can xac thuc
    Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/create', [StoreController::class, 'create']);
    Route::get('/findStoreByOwnId/{user_id}', [StoreController::class, 'findStoreByOwnId']);
    });
   
    //Cac chuc nang can xac thuc va co store
    Route::middleware(['auth:sanctum', 'is-store'])->group(function () {
        Route::get('/getProductsList', [StoreController::class, 'getProductsList']);
        Route::get('/getOrderList', [StoreController::class, 'getOrderList']);
        Route::get('/myStore', [StoreController::class, 'myStore']);
        Route::post('/update', [StoreController::class, 'update_profile']);
        Route::delete('/delete-store', [StoreController::class,'deleteStore']);
        Route::apiResource('user-notifications', UserNotificationController::class);
        Route::apiResource('store-notifications', StoreNotificationController::class);
        Route::apiResource('messages', MessageController::class);
        Route::apiResource('followers', FollowerController::class);
       
    });
    
});

//OrderController
Route::prefix('order')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [OrderController::class, 'getAllOrder']);
    Route::post('/create', [OrderController::class, 'createOrderFromCart']);
    Route::post('/createDirectOrder', [OrderController::class, 'createDirectOrder']);
    Route::get('/getById/{id}', [OrderController::class,'displayOrder']);
    Route::put('/cancel/{id}', [OrderController::class,'cancelOrderByUser']);

});




//CartController
Route::prefix('cart')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CartController::class, 'viewCart']);
    Route::post('/add/{product_id}', [CartController::class, 'addToCart']);
    Route::put('/update/{product_id}', [CartController::class,'updateCart']);
    Route::delete('/delete/{product_id}', [CartController::class,'removeFromCart']);
    Route::delete('/clear', [CartController::class,'clearCart']);
    Route::get('/count', [CartController::class, 'count']);
});




//AuthController
Route::prefix('auth')->group(function () {
    // Cac chuc nang khong can xac thuc
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Cac chuc nang can xac thuc
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/check-auth', [AuthController::class, 'checkAuthUser']);
    });
});