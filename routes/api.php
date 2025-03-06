<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ImageDetailController;
use App\Http\Controllers\RoleController;

Route::middleware('auth:sanctum')->get('/user/profile', function (Request $request) {
    return $request->user();
});

//CategoryController
Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/create', [CategoryController::class, 'create']);
    Route::put('/update/{id}', [CategoryController::class, 'update']);
    Route::delete('/delete/{id}', [CategoryController::class, 'delete']);
});

//UserController
Route::prefix('user')->group(function () {
    // Cac route khong can xac thuc (se chinh sua de danh cho AdminAdmin)
    Route::get('/', [UserController::class, 'index']);
    Route::delete('/delete/{id}', [UserController::class, 'delete']);

    // Cac route danh cho nguoi dung, can xac thuc tai khoan moi co the dung
    Route::middleware('auth:sanctum')->group(function () {
        Route::put('/update-profile', [UserController::class, 'updateProfile']);
        Route::delete('/delete', [UserController::class, 'deleteUser']);
        Route::get('/getProfile', [UserController::class, 'getProfile']);
    });
});


// RoleController
Route::prefix('role')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/create', [RoleController::class, 'create']);
    Route::put('/update/{id}', [RoleController::class, 'update']);
    Route::delete('/delete/{id}', [RoleController::class, 'delete']);
});

//StoreController
Route::prefix('store')->group(function () {
    Route::get('/', [StoreController::class, 'index']);
    Route::get('/findStoreById/{store_id}', [StoreController::class, 'findStoreById']);
    Route::get('/findStoreByOwnId/{user_id}', [StoreController::class, 'findStoreByOwnId']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/myStore', [StoreController::class, 'myStore']);
        Route::post('/create', [StoreController::class, 'create']);
        Route::put('/update', [StoreController::class, 'update_profile']);
        Route::delete('/delete-store', [StoreController::class, 'deleteStore']);
    });
});



Route::prefix('auth')->group(function () {
    // Các route không cần bảo vệ (không yêu cầu token)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Các route bảo vệ bởi token (sử dụng Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/check-auth', [AuthController::class, 'checkAuthUser']);
    });
});
// 🛒 ProductController - API CRUD cho Sản phẩm
Route::prefix('product')->group(function () {
    Route::get('/', [ProductController::class, 'index']);         // Lấy danh sách sản phẩm
    Route::get('/{id}', [ProductController::class, 'show']);      // Lấy thông tin sản phẩm theo ID
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/create', [ProductController::class, 'store']);  // Thêm sản phẩm mới
        Route::put('/update/{id}', [ProductController::class, 'update']);  // Cập nhật sản phẩm
        Route::delete('/delete/{id}', [ProductController::class, 'destroy']); // Xóa sản phẩm
    });
});

// 📷 ImageDetailController - API CRUD cho Ảnh sản phẩm
Route::prefix('image')->group(function () {
    Route::get('/', [ImageDetailController::class, 'index']);         // Lấy danh sách ảnh
    Route::get('/{id}', [ImageDetailController::class, 'show']);      // Lấy ảnh theo ID
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/create', [ImageDetailController::class, 'store']);  // Thêm ảnh mới
        Route::put('/update/{id}', [ImageDetailController::class, 'update']);  // Cập nhật ảnh
        Route::delete('/delete/{id}', [ImageDetailController::class, 'destroy']); // Xóa ảnh
    });
});
