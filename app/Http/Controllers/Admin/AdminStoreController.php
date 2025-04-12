<?php

namespace App\Http\Controllers\Admin; // Namespace đúng

use App\Http\Controllers\Controller; // Import Controller cơ sở
use Illuminate\Http\Request;
use App\Models\Store; // Import Store model
use App\Models\User; // Import User model nếu cần lấy thông tin owner
use Illuminate\Support\Facades\Log; // Để ghi log nếu cần

class AdminStoreController extends Controller
{
 
    public function index(Request $request)
    {
        $query = Store::with('owner:id,firstName,lastName,email'); // Lấy thông tin cơ bản của owner

        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('storeName', 'like', $searchTerm)
                  ->orWhereHas('owner', function($ownerQuery) use ($searchTerm) {
                      $ownerQuery->where('name', 'like', $searchTerm)
                                 ->orWhere('email', 'like', $searchTerm);
                  });
            });
        }

        $limit = $request->input('limit', 10); // Số lượng item mỗi trang
        $stores = $query->orderBy('created_at', 'desc')->paginate($limit);

        return response()->json([
            'success' => true,
            'stores' => $stores->items(),
            'pagination' => [
                'currentPage' => $stores->currentPage(),
                'totalPages' => $stores->lastPage(),
                'totalStores' => $stores->total(),
                'perPage' => $stores->perPage(),
            ]
        ]);
    }


    public function listStores()
    {
        try {
            $stores = Store::select('id', 'storeName')->where('status', 'approved')->orderBy('storeName')->get();
            return response()->json(['success' => true, 'stores' => $stores]);
        } catch (\Exception $e) {
            Log::error("Error fetching store list: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Could not fetch stores.'], 500);
        }
    }



    public function approveStore(Request $request, $storeId) // Nhận storeId từ route
    {
        try {
            $store = Store::find($storeId);
            if (!$store) {
                return response()->json(['success' => false, 'message' => 'Store not found.'], 404);
            }

            if ($store->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Store is not pending approval.'], 400);
            }

            $store->status = 'approved';
            $store->save();


            return response()->json(['success' => true, 'message' => 'Store approved successfully.', 'store' => $store]);

        } catch (\Exception $e) {
            Log::error("Error approving store {$storeId}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to approve store.'], 500);
        }
    }

  
    public function rejectStore(Request $request, $storeId)
    {
         try {
            $store = Store::find($storeId);
            if (!$store) {
                return response()->json(['success' => false, 'message' => 'Store not found.'], 404);
            }

             if ($store->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Store is not pending approval.'], 400);
            }

            $store->status = 'rejected';
            $store->save();


            return response()->json(['success' => true, 'message' => 'Store rejected successfully.', 'store' => $store]);

        } catch (\Exception $e) {
            Log::error("Error rejecting store {$storeId}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to reject store.'], 500);
        }
    }

}