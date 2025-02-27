<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;



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

    //Tao user
    public function create(Request $request)
    {
        Log::info('UserController thuc thi');
        
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6'
        ]);
        

    if ($validator->fails()) {
        $data = [
            'status' => 422,
            'message' => $validator->messages()
        ];
        return response()->json($data, 422);

    } else {
        $user = new User;
        $user->email = $request->email;
        $user->password = $request->password;

        $user->save();
       

        $data = [
            'status'   => 200,
            'message'  => 'Successfully create new user',
            'user' => $user,  // Trả về đối tượng User vừa lưu
        ];

        return response()->json($data, 200);
    }
}

//Chinh sua UserUser
public function update_Profile(Request $request, int $id)
{
    //Kiem tra du lieu hop lele
    $validator = Validator::make($request->all(), [
        'firstName' => 'nullable',
        'lastName'=> 'nullable',
        'avatar' => 'nullable',
        'phoneNumber' => 'nullable',
    ]);

    if ($validator->fails()) {
        $data = [
            'status' => 422,
            'message' => $validator->messages()
        ];
        return response()->json($data, 422);
    } else {

        //Tim kiem User theo id dc truyen vaovao
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        //Gan gia tri moi cho thuoc tinh namename
        $user->firstName = $request->firstName;
        $user->lastName = $request->lastName;
        $user->phoneNumber = $request->phoneNumber;
        $user->avatar = $request->avatar;

        //Luu vao csdlcsdl
        $user->save();

        //Tao noi dung thong bao de returnreturn
        $data = [
            'status'   => 200,
            'message'  => "Successfully update User with id = $id",
            'user' => $user,  // Trả về đối tượng User mới cập nhật
        ];

        return response()->json($data, 200);
    }

    }


    //Xoa UserUser
   


}