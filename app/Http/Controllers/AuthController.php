<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản mới.
     */
    public function register(Request $request)
    {
        // Validate dữ liệu đầu vào
        Log::info('UserController thực thi chức năng đăng ký user');
        
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            // 'name' => 'required',
            // 'phoneNumber' =>'required'
        ]);
        
        if ($validator->fails()) {
            $data = [
                'status'  => 422,
                'message' => $validator->messages()
            ];
            return response()->json($data, 422);
        } else {
            $user = new User;
            $user->email = $request->email;
            $user->role_id = 2; //Role 2 la User
            // $user->name = $request->name;
            // $user->phoneNumber = $request->phoneNumber;
            // Bam mat khau su dungdung Bcrypt
            $user->password = Hash::make($request->password);
            
            $user->save();
           
            $data = [
                'status'  => 200,
                'message' => 'Successfully created new user',
                'user'    => $user,
            ];
            
            return response()->json($data, 200);
        }
    }

    /**
     * Đăng nhập tài khoản.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);
    
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status'  => 401,
                'message' => 'Invalid credentials'
            ], 401);
        }
    
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status'  => 401,
                'message' => 'User is not authenticated.'
            ], 401);
        }
        $token = $user->createToken('api-token')->plainTextToken;

    
        return response()->json([
            'status'  => 200,
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token,
        ], 200);
    }
    
    /**
     * Đăng xuất tài khoản.
     */
    public function logout(Request $request)
{
    // Xoá token hiện tại của người dùng
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'status'  => 200,
        'message' => 'Logout successful',
    ], 200);
}


    /**
     * Kiểm tra người dùng đã đăng nhập hay chưa.
     */
    public function checkAuthUser(Request $request)
    {
        if (Auth::check()) {
            return response()->json([
                'status'  => 200,
                'message' => 'User is authenticated.',
                'user'    => Auth::user()
            ], 200);
        }

        return response()->json([
            'status'  => 401,
            'message' => 'User is not authenticated.'
        ], 401);
    }
}