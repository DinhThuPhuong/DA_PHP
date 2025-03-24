<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Store;

class CheckUserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra trạng thái đăng nhập
        if (!auth()->check()) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }

        $user = auth()->user();
         // Kiem tra role cua nguoi dung
         if ($user->role_id == 1) {  //role = 1
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized. You do not have permission to access this resource.'
            ], 403);
        }
        
        // Kiem tra nguoi dung co store chua
        $store = Store::where('ownId', $user->id)->first();
        
        // Neu da co store thi thong bao voi loi 403
        if ($store) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized. You already have a store.'
            ], 403);
        }

       

        
        return $next($request);
    }
}