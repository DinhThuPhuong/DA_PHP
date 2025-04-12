<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Store;

class CheckStoreAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }

        $user = auth()->user();

        $store = Store::where('ownId', $user->id)->first();

        if (!$store) {
             return response()->json([
                'status' => 403, 
                'message' => 'No store found for this user.',
                'reason' => 'not_found' 
            ], 403);
        }

        if ($store->status !== 'approved') {
             return response()->json([
                'status' => 403, 
                'message' => 'Your store is currently ' . $store->status . '. Please wait for approval or contact support.',
                'reason' => $store->status 
            ], 403);
        }

        $request->merge(['store' => $store]);

        return $next($request);
    }
}