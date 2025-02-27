<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\StudentController;


Route::get('/', function () {
    return view('welcome');
});

// Route::get('/student', [StudentController::class, 'index']);

// Route::post('/student/create', [StudentController::class, 'create']);