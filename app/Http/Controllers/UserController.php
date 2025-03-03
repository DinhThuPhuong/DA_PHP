<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash; // Đảm bảo import Hash



class UserController extends Controller
{

    //Tao moi User
    public function index()
    {
        
        $user = User::all();
        $data = [
            'status'=>200,
            'user' => $user
        ];
        return response()->json($data, 200);

    }


// //Chinh sua thong tin nguoi dung dang dang nhapnhap
// public function updateProfile(Request $request)
// {
//     Log::info('Chay updateupdate');
//     //Kiem tra du lieu hop le
//     $validator = Validator::make($request->all(), [
//         'firstName' => 'nullable',
//         'lastName'=> 'nullable',
//         'avatar' => 'nullable',
//         'phoneNumber' => 'nullable',
//     ]);

//     if ($validator->fails()) {
//         $data = [
//             'status' => 422,
//             'message' => $validator->messages()
//         ];
//         return response()->json($data, 422);
//     } else {

//         //Lay thong tin user dang login
//         $user = Auth::user();
//         if (!$user) {
//             return response()->json([
//                 'status'  => 401,
//                 'message' => 'User not authenticated.'
//             ], 401);
//         }

//         //Gan gia tri moi cho user neu co thong tin moi
//         if($request->has('firstName')&&trim($request->firstName)!== '') 
//             $user->firstName = $request->firstName;
//         if($request->has('lastName')&&trim($request->lastName)!== '') 
//         $user->lastName = $request->lastName;
        
//         if($request->has('phoneNumber')&&trim($request->phoneNumber)!='') 
//             $user->phoneNumber = $request->phoneNumber;
//         if($request->has('avatar') &&trim($request->avatar)!='')
//             $user->avatar = $request->avatar;

//         //Luu vao csdl
//         $user->save();

//         //Tao noi dung thong bao de returnreturn
//         $data = [
//             'status'   => 200,
//             'message'  => "Successfully updated user information",
//             'user' => $user,  // Trả về đối tượng User mới cập nhật
//         ];

//         return response()->json($data, 200);
//     }


//     }
 //Nguoi dung chinh sua thong tin Profile
 public function updateProfile(Request $request)
 {
     // Validate dữ liệu đầu vào
     $validator = Validator::make($request->all(), [
         'firstName' => 'nullable|string|max:255',
         'lastName' => 'nullable|string|max:255',
        //  'avatar'      => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048', // Validate file ảnh
        'avatar' => 'nullable',
         'phoneNumber' => 'nullable|string|max:20',
     ]);
     
     if ($validator->fails()) {
         return response()->json([
             'status'  => 422,
             'message' => $validator->messages()
         ], 422);
     }
     
     // Lấy thông tin user đang đăng nhập
     $user = Auth::user();
     
     if (!$user) {
         return response()->json([
             'status'  => 401,
             'message' => 'User not authenticated.'
         ], 401);
     }
     
     // Cap nhat thong tin moi neu nguoi dung nhap thong tin moi
     if($request->has('firstName')&&trim($request->firstName)!== '') 
         $user->firstName = $request->firstName;
    if($request->has('lastName')&&trim($request->lastName)!== '') 
    $user->lastName = $request->lastName;

     if($request->has('phoneNumber')&&trim($request->phoneNumber)!='') 
         $user->phoneNumber = $request->phoneNumber;
 
     // Nếu có file avatar được upload, xử lý upload lên Cloudinary
     // if ($request->hasFile('avatar')) {
     //     $file = $request->file('avatar');
     //     try {
     //         // Upload file lên Cloudinary, có thể thêm các tùy chọn (ví dụ: folder)
     //         $result = Uploader::upload($file->getRealPath(), [
     //             'folder' => 'user_avatars'
     //         ]);
     //         // Lấy URL an toàn của hình ảnh từ kết quả trả về
     //         $user->avatar = $result['secure_url'] ?? null;
     //     } catch (\Exception $e) {
     //         return response()->json([
     //             'status'  => 500,
     //             'message' => 'Failed to upload avatar: ' . $e->getMessage()
     //         ], 500);
     //     }
     // }
     if($request->has('avatar') &&trim($request->avatar)!='')
         $user->avatar = $request->avatar;
     
     // Luu thong tin vao csdl
     $user->save();
     
     return response()->json([
         'status'  => 200,
         'message' => 'Successfully updated user profile.',
         'user'    => $user,
     ], 200);
 }
    //Nguoi dung xoa tai khoan cua ban than
    public function deleteUser(Request $request)
{
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'status'=> 404,
            'message'=> 'User not authenticated.'
            ],404);
        }
    $user->delete();
    return response()->json([
        'status'=> 200,
        'message'=> 'Succesfully deleted user']);
        

}


    //Xoa UserUser chuc nang danh cho admin
    public function delete(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message'=> "User with $id not found",
            ], 404);
        }
        $user->delete();

        $data = [
            "status"=> 200,
            "message"=> "Successfully deleted User"
        ];

        return response()->json($data, 200);
        
    }

    //Hien thi thong tin profile cua nguoi dung
    public function getProfile(Request $request)
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