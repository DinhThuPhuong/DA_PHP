<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    //Hien thi danh sach tat ca store 
    public function index()
    {
        $store = Store::all();

        $data = [
            'status' => 200,
            'store' => $store
        ];
        return response()->json($data, 200);

    }
    public function create(Request $request, int $user_id){

    //kiem tra du lieu dau vao, ten store khong cho phep trung 
    $validator = Validator::make($request->all(), [
        'storeName'   => 'required|max:255|unique:store,storeName',
        'description' => 'required',
        'avatar'      => 'required'
    ]);
     
    if ($validator->fails())
    {
        $data = [
            'status' => 422,
            'message' => $validator->messages()
        ];
        return response()->json($data,422);

    }
    else
    { 
        //Kiem tra su ton tai cua user dang ki mo store
        $user = User::find($user_id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
        else{
            //Kiem tra xem nguoi dung da dang ki tao store nao chua 
            $store1 = Store::where('ownId', $user->id)->first();
            if($store1)
            {
                return response()->json([
                 'message' => "User with ID $user_id has already registered a store."
                    ],500);

            }
            //Neu chua thi tien hanh tao store moi cho nguoi dung

            //Khoi tao store de gan gia tri
            $store = new Store;
            $store->ownId = $user_id;
            $store->storeName = $request->storeName;
            $store->description = $request->description;
            $store->avatar = $request->avatar;

            //Luu vao csdl
            $store->save();

            $data = [
                'status' => 200,
                'message' => 'Successfully created a new store.',
                'store' => $store
            ];
            
            //Tra ve du lieu 
            return response()->json($data,200);

            }

        
    }
    }
   

}