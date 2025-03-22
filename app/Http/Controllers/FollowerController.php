<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Follower;
use Illuminate\Support\Facades\Auth;

class FollowerController extends Controller
{
    // Lấy danh sách các store mà user đang theo dõi
    public function index()
    {
        $userId = Auth::id();
        $followers = Follower::where('user_id', $userId)->get();

        return response()->json($followers);
    }

    // User theo dõi Store
    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:store,id', // Đảm bảo store_id tồn tại
        ]);

        $userId = Auth::id();
        $storeId = $request->store_id;

        // Kiểm tra nếu user đã follow store này rồi
        $exists = Follower::where('user_id', $userId)
                          ->where('store_id', $storeId)
                          ->exists();

        if ($exists) {
            return response()->json(['message' => 'You are already following this store'], 400);
        }

        // Tạo bản ghi mới trong bảng follower
        $follower = Follower::create([
            'user_id' => $userId,
            'store_id' => $storeId,
        ]);

        return response()->json(['message' => 'Followed successfully', 'data' => $follower], 201);
    }

    // User hủy theo dõi Store
    public function destroy($storeId)
    {
        $userId = Auth::id();

        $follower = Follower::where('user_id', $userId)
                            ->where('store_id', $storeId)
                            ->first();

        if (!$follower) {
            return response()->json(['message' => 'You are not following this store'], 400);
        }

        $follower->delete();
        return response()->json(['message' => 'Unfollowed successfully']);
    }

    public function getStoreFollowers()
{
    $storeId = Auth::id(); // Giả sử store đăng nhập bằng token

    // Lấy danh sách user theo dõi store
    $followers = Follower::where('store_id', $storeId)
        ->with('user:id,name,email') // Lấy thêm thông tin user
        ->get();

    return response()->json($followers);
}
}
