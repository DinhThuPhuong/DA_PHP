<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Để xử lý date range

class OrderController extends Controller
{
    public function getAllOrdersAdmin(Request $request)
    {
        try {
            $query = Order::with(['user:id,email,firstName,lastName', 'store:id,storeName']); // Eager load relations

            // Filtering
            if ($request->filled('status')) {
                $query->where('shipping_status', $request->status); // Lọc theo shipping_status
            }
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('id', 'LIKE', "%{$searchTerm}%") // Tìm theo ID đơn hàng
                      ->orWhereHas('user', function($userQuery) use ($searchTerm) { // Tìm theo email user
                          $userQuery->where('email', 'LIKE', "%{$searchTerm}%");
                      })
                      ->orWhereHas('store', function($storeQuery) use ($searchTerm) { // Tìm theo tên store
                          $storeQuery->where('storeName', 'LIKE', "%{$searchTerm}%");
                      });
                     // Thêm tìm kiếm theo tên người nhận nếu cần (từ cột riêng)
                      // ->orWhere('shipping_first_name', 'LIKE', "%{$searchTerm}%")
                      // ->orWhere('shipping_last_name', 'LIKE', "%{$searchTerm}%");
                });
            }
            if ($request->filled('from')) {
                try {
                    $fromDate = Carbon::parse($request->from)->startOfDay();
                    $query->where('created_at', '>=', $fromDate);
                } catch (\Exception $e) {
                    Log::warning('Invalid date format for "from" filter: ' . $request->from);
                }
            }
            if ($request->filled('to')) {
                 try {
                    $toDate = Carbon::parse($request->to)->endOfDay();
                    $query->where('created_at', '<=', $toDate);
                 } catch (\Exception $e) {
                    Log::warning('Invalid date format for "to" filter: ' . $request->to);
                 }
            }

            // Ordering and Pagination
            $limit = $request->input('limit', 10); // Mặc định 10 item/trang
            $orders = $query->orderBy('created_at', 'desc')->paginate($limit);

            return response()->json([
                'success' => true,
                'orders' => $orders->items(),
                'pagination' => [
                    'currentPage' => $orders->currentPage(),
                    'totalPages' => $orders->lastPage(),
                    'totalOrders' => $orders->total(),
                    'perPage' => $orders->perPage(),
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error("Admin Fetch Orders Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to retrieve orders.'], 500);
        }
    }

}
