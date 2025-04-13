<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB; // Import DB Facade if needed for more complex queries

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        try {
            $userCount = User::count();
            $storeStats = Store::selectRaw('count(*) as total, sum(case when status = "approved" then 1 else 0 end) as approved, sum(case when status = "pending" then 1 else 0 end) as pending')
                               ->first();
            $productCount = Product::count(); // Có thể thêm điều kiện where('isValidated', true) nếu chỉ tính SP đã duyệt
            $orderCount = Order::count();

            $recentOrders = Order::with('user:id,firstName,lastName,email') // Lấy thông tin user cơ bản
                                 ->latest() // Sắp xếp theo ngày tạo mới nhất
                                 ->limit(5) // Giới hạn 5 đơn hàng
                                 ->get();

            $recentPendingStores = Store::where('status', 'pending')
                                        ->with('owner:id,firstName,lastName,email') // Lấy thông tin chủ sở hữu
                                        ->latest()
                                        ->limit(5)
                                        ->get();

            $stats = [
                'users' => $userCount,
                'stores' => [
                    'total' => $storeStats->total ?? 0,
                    'approved' => $storeStats->approved ?? 0,
                    'pending' => $storeStats->pending ?? 0,
                ],
                'products' => $productCount,
                'orders' => $orderCount,
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'recentOrders' => $recentOrders,
                'recentPendingStores' => $recentPendingStores,
            ], 200);

        } catch (\Exception $e) {
            Log::error("Admin Stats Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to retrieve dashboard statistics.'], 500);
        }
    }
}
