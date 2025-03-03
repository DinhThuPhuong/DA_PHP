<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\JwtMiddleware;

Route::middleware('auth:sanctum')->get('/user/profile', function( Request $request)
{
    return $request->user();
});

//CategoryController
Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/create', [CategoryController::class, 'create']);
    Route::put('/update/{id}', [CategoryController::class, 'update']);
    Route::delete('/delete/{id}', [CategoryController::class,'delete']);
  
});

//UserController
Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/display/{id}', [UserController::class, 'display']);
    Route::post('/create', [UserController::class, 'create']);
    Route::put('/update/{id}', [UserController::class,'update_Profile']);
    Route::delete('/delete/{id}', [UserController::class,'delete']);

  
});

//StoreController
Route::prefix('store')->group(function () {
    Route::get('/', [StoreController::class, 'index']);
    Route::post('/create/{user_id}', [StoreController::class, 'create']);
    Route::put('/update/{user_id}', [StoreController::class, 'update_profile']);
    Route::get('/findStoreById/{store_id}', [StoreController::class, 'findStoreById']);
    Route::get('/findStoreByOwnId/{user_id}', [StoreController::class, 'findStoreByOwnId']);
    
});


//JWT
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::middleware('jwt.auth')->get('user', [AuthController::class, 'getUser']);

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::get('user', [AuthController::class, 'getUser']);
});