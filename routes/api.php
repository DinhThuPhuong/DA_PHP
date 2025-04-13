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
use App\Models\StoreNotification;
use App\Http\Controllers\VnPayController;
use App\Http\Controllers\Admin\AdminStoreController;

Route::middleware('auth:sanctum')->get('/user/profile', function (Request $request) {
    return $request->user();
});

Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::middleware(['auth:sanctum', 'is-admin'])->group(function () {
        Route::post('/create', [CategoryController::class, 'create']);
        Route::put('/update/{id}', [CategoryController::class, 'update']);
        Route::delete('/delete/{id}', [CategoryController::class, 'delete']);
    });
});

Route::prefix('admin')->middleware(['auth:sanctum', 'is-admin'])->group(function () {
    Route::get('/stores', [AdminStoreController::class, 'index']);
    Route::post('/stores/{storeId}/approve', [AdminStoreController::class, 'approveStore']);
    Route::post('/stores/{storeId}/reject', [AdminStoreController::class, 'rejectStore']);
    Route::get('/stores/list', [AdminStoreController::class, 'listStores']);
});

Route::prefix('user')->group(function () {
    Route::middleware(['auth:sanctum', 'is-admin'])->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::delete('/deleteByAdmin/{id}', [UserController::class, 'deleteUserByAdmin']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/getProfile', [UserController::class, 'getProfile']);
        Route::post('/update-profile', [UserController::class, 'updateProfile']);
        Route::delete('/delete', [UserController::class, 'deleteByUser']);
    });

    Route::middleware('auth:sanctum')->group(function() {
        Route::post('/notifications', [StoreNotificationController::class, 'store']); // User thông thường gửi thông báo cho store
    });
});

Route::prefix('product')->group(function () {
    Route::get('/', [ProductController::class, 'getAllProduct']);
    Route::get('/search/{keyword}', [ProductController::class, 'searchProduct']);
    Route::get('/display/{id}', [ProductController::class, 'display']);

    Route::middleware(['auth:sanctum', 'is-store'])->group(function () {
        Route::post('/create', [ProductController::class, 'create']);
        Route::post('/update/{id}', [ProductController::class, 'update']);
        Route::delete('/delete/{id}', [ProductController::class, 'delete']);
    });
});

Route::prefix('role')->middleware(['auth:sanctum', 'is-admin'])->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/create', [RoleController::class, 'create']);
    Route::put('/update/{id}', [RoleController::class, 'update']);
    Route::delete('/delete/{id}', [RoleController::class, 'delete']);
});

Route::prefix('store')->group(function () {
    Route::get('/', [App\Http\Controllers\StoreController::class, 'index']); // Danh sách store đã duyệt
    Route::get('/findStoreById/{store_id}', [App\Http\Controllers\StoreController::class, 'findStoreById']);
    Route::get('/findStoreByStoreName/{storeName}', [App\Http\Controllers\StoreController::class, 'findStoreByStoreName']);
});

// === Các Route cần User Đăng nhập ===
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/store/create', [App\Http\Controllers\StoreController::class, 'create']); // User yêu cầu mở store
    // Thêm các route khác của user ở đây (ví dụ: follow/unfollow)
});

// === Các Route cần User là Chủ Store (Store Owner) ===
Route::middleware(['auth:sanctum', 'is-store'])->group(function () {
    Route::get('/store/myStore', [App\Http\Controllers\StoreController::class, 'myStore']); // Lấy thông tin store của tôi
    Route::get('/store/products', [App\Http\Controllers\StoreController::class, 'getProductsList']); // Lấy SP của store tôi
    Route::get('/store/orders', [App\Http\Controllers\StoreController::class, 'getOrderList']); // <-- Lấy Order của store tôi (ĐÚNG CHỖ)
    Route::post('/store/update', [App\Http\Controllers\StoreController::class, 'update_profile']); // Cập nhật store tôi
    Route::delete('/store/delete', [App\Http\Controllers\StoreController::class, 'deleteStore']); // Xóa store tôi
    Route::put('/store/orders/{order_id}/status', [App\Http\Controllers\StoreController::class, 'updateOrderStatus']); // Ví dụ route cập nhật trạng thái order
    Route::put('/store/orders/{order_id}/cancel', [App\Http\Controllers\StoreController::class, 'cancelOrderByStore']); // Ví dụ route hủy order
});

Route::prefix('usernotifications')->group(function () {
    Route::middleware('auth:sanctum')->group(function () { // Bỏ is-user
        Route::get('/get', [UserNotificationController::class, 'index']);
        Route::get('/detail/{id}', [UserNotificationController::class, 'show']);
        // Route::post('/create', [UserNotificationController::class, 'store']); // User không tự tạo notif cho mình
    });

    Route::middleware(['auth:sanctum', 'is-admin'])->group(function () {
        Route::put('/update/{id}', [UserNotificationController::class, 'update']);
        Route::delete('/delete/{id}', [UserNotificationController::class, 'destroy']);
    });
});

Route::prefix('storenotification')->group(function () {
    Route::middleware(['auth:sanctum', 'is-store'])->group(function () {
        Route::get('/get', [StoreNotificationController::class, 'index']);
        Route::get('/detail/{id}', [StoreNotificationController::class, 'show']);
        // Route::post('/create', [StoreNotificationController::class, 'store']); // Store không tự tạo notif cho mình
    });
    Route::middleware(['auth:sanctum', 'is-admin'])->group(function () {
        Route::put('/update/{id}', [StoreNotificationController::class, 'update']);
        Route::delete('/delete/{id}', [StoreNotificationController::class, 'destroy']);
    });
});

Route::prefix('followers')->middleware('auth:sanctum')->group(function () {
    Route::get('/get', [FollowerController::class, 'index']); // User xem store mình follow
    Route::post('/create', [FollowerController::class, 'store']); // User follow store
    Route::delete('/delete/{storeId}', [FollowerController::class, 'destroy']); // User unfollow store
});

Route::prefix('message')->middleware('auth:sanctum')->group(function () { // Chỉ cần auth chung
    // Cho User đọc/gửi tin nhắn với các store
    Route::get('/getUserMS', [MessageController::class, 'show']); // Cần logic trong controller để lấy theo user_id
    Route::post('/UserCreate', [MessageController::class, 'store']); // Gửi tin nhắn từ user đến store
    Route::put('/userUpdate/{id}', [MessageController::class, 'update']); // User sửa tin nhắn? (Ít dùng)
    Route::delete('/UserDelete/{id}', [MessageController::class, 'destroy']); // User xóa tin nhắn? (Ít dùng)

    // Cho Store đọc/gửi tin nhắn với các user
    Route::middleware('is-store')->group(function () {
        Route::get('/getStoreMS', [MessageController::class, 'show']); // Cần logic trong controller để lấy theo store_id
        Route::post('/StoreCreate', [MessageController::class, 'store']); // Gửi tin nhắn từ store đến user
        Route::put('/StoreUpdate/{id}', [MessageController::class, 'update']); // Store sửa tin nhắn? (Ít dùng)
        Route::delete('/StoreDelete/{id}', [MessageController::class, 'destroy']); // Store xóa tin nhắn? (Ít dùng)
    });
});

Route::prefix('order')->middleware('auth:sanctum')->group(function () { // User/Admin/Store đều có thể xem order của mình?
    Route::get('/', [OrderController::class, 'getAllOrder']); // Chỉ user xem order của họ
    Route::get('/getOrdersByStatus/{status}', [OrderController::class, 'getOrdersByStatus']); // Chỉ user xem order của họ
    Route::post('/create', [OrderController::class, 'createOrderFromCart']);
    Route::post('/createDirectOrder', [OrderController::class, 'createDirectOrder']);
    Route::get('/getById/{id}', [OrderController::class, 'displayOrder']); // User xem chi tiết order của họ
    Route::put('/cancel/{id}', [OrderController::class, 'cancelOrderByUser']); // User hủy order của họ
});

Route::prefix('cart')->middleware('auth:sanctum')->group(function () { // Bỏ is-user
    Route::get('/', [CartController::class, 'viewCart']);
    Route::post('/add/{product_id}', [CartController::class, 'addToCart']);
    Route::put('/update/{product_id}', [CartController::class, 'updateCart']);
    Route::delete('/delete/{product_id}', [CartController::class, 'removeFromCart']);
    Route::delete('/clear', [CartController::class, 'clearCart']);
    Route::get('/count', [CartController::class, 'count']);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/check-auth', [AuthController::class, 'checkAuthUser']);
    });
});

Route::get('/vnpay/return', [OrderController::class, 'handleVnpayReturn']);