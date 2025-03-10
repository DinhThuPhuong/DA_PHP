<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\CloudinaryAdapter;

class UserController extends Controller
{
    protected $cloudinary;

    public function __construct(CloudinaryAdapter $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

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
    

    //Nguoi dung chinh sua thong tin Profile
    public function updateProfile(Request $request)
    {
        Log::info('Request Data:', $request->all());

        // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'firstName'        => 'required|string|max:255',
            'lastName'        => 'required|string|max:255',
            'avatar'      => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048', // Validate file ảnh
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
        if($request->has('name') && trim($request->name) !== '') 
            $user->name = $request->name;
        if($request->has('phoneNumber') && trim($request->phoneNumber) != '') 
            $user->phoneNumber = $request->phoneNumber;

        
        if ($request->hasFile('avatar')) {
            try {
                $file = $request->file('avatar');
                
                // Upload ảnh lên Cloudinary
                $uploadResult = $this->cloudinary->upload(
                    $file,
                    [
                        'folder' => 'avatars',
                        'public_id' => time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    ]
                );
                
                // Lưu URL của ảnh vào database
                $user->avatar = $uploadResult['secure_url'];
                
                Log::info('Cloudinary Upload Result:', $uploadResult);
            } catch (\Exception $e) {
                Log::error('Cloudinary Upload Error: ' . $e->getMessage());
                
                return response()->json([
                    'status' => 500,
                    'message' => 'Error uploading image: ' . $e->getMessage()
                ], 500);
            }
        }
        
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
}