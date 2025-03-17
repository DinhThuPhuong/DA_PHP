<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Store;

class CheckStoreAuth
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
        //Kiem tr trang thai dang nhap
        if (!auth()->check()) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }

        $user = auth()->user();
        
        // Kiem tra nguoi dung co store hay khong
        $store = Store::where('ownId', $user->id)->first();
        //Neu khong co store thi tra ve loi
        if (!$store) {
            return response()->json([
                'status' => 403,
                'message' => 'Unauthorized. You need to register a store first.'
            ], 403);
        }

        //Neu co store thi cho phep truy cap
        return $next($request);
    }
}