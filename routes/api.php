<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->get('/user', function( Request $request)
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
    Route::post('/create', [UserController::class, 'create']);
    Route::put('/update/{id}', [UserController::class,'update_Profile']);

  
});