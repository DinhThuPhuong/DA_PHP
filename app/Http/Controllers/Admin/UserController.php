<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role; // Giả sử bạn có Model Role
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function getAllUsersAdmin(Request $request)
    {
         try {
             $query = User::with('role'); // Eager load role

             // Filtering
             if ($request->filled('role')) {
                 // Lọc theo role_id hoặc roleName tùy thuộc vào cấu trúc DB và request
                 // Ví dụ lọc theo roleName (cần join hoặc whereHas)
                 $roleName = $request->role;
                 $query->whereHas('role', function($q) use ($roleName) {
                     $q->where('roleName', $roleName); // Giả sử bảng roles có cột roleName
                 });
                 // Hoặc nếu gửi role_id:
                 // $query->where('role_id', $request->role);
             }
             if ($request->filled('search')) {
                 $searchTerm = $request->search;
                 $query->where(function($q) use ($searchTerm) {
                     $q->where('email', 'LIKE', "%{$searchTerm}%")
                       ->orWhere('firstName', 'LIKE', "%{$searchTerm}%")
                       ->orWhere('lastName', 'LIKE', "%{$searchTerm}%");
                 });
             }

             // Ordering and Pagination
             $limit = $request->input('limit', 10);
             $users = $query->orderBy('created_at', 'desc')->paginate($limit);

             return response()->json([
                 'success' => true,
                 'users' => $users->items(),
                 'pagination' => [
                     'currentPage' => $users->currentPage(),
                     'totalPages' => $users->lastPage(),
                     'totalUsers' => $users->total(),
                     'perPage' => $users->perPage(),
                 ]
             ], 200);

         } catch (\Exception $e) {
             Log::error("Admin Fetch Users Error: " . $e->getMessage());
             return response()->json(['success' => false, 'message' => 'Failed to retrieve users.'], 500);
         }
    }

     // Thêm các hàm khác cho Admin quản lý User nếu cần (update status, role, delete...)
     // Ví dụ:
     /*
     public function updateUserStatusAdmin(Request $request, $id) { ... }
     public function updateUserRoleAdmin(Request $request, $id) { ... }
     // Hàm delete có thể dùng lại từ UserController chính hoặc viết riêng ở đây
     */
}
