<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

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

    //Nguoi dung tao store
    public function create(Request $request){

    //Kiem tra su ton tai cua user dang ki mo store
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'message' => 'User not authenticated.',
        ], 404);
    }

    // Kiem tra xem nguoi dung da co store chua
    $store1 = Store::where('ownId', $user->id)->first();
    if ($store1) {
        return response()->json([
            'message' => "User with ID {$user->id} has already registered a store."
        ], 500);
    }


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
        // Neu chua thi tien hanh dang ki store
        $store = new Store;
        $store->ownId = $user->id;  // Su dung id cua nguoi dung dang ki store
        $store->storeName = $request->storeName;
        $store->description = $request->description;
        $store->avatar = $request->avatar;

        // LLuu vao csdl 
        $store->save();

        $data = [
            'status' => 200,
            'message' => 'Successfully created a new store.',
            'store' => $store
        ];

        // Tra du lieu ve
        return response()->json($data, 200);

}
    }


    //Nguoi dung update thong tin store
public function update_profile(Request $request)
{
 
    //Kiem tra trang thai dang nhap
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'message' => 'User not authenticated.',
        ], 404);
    }

    // Kiem tra nguoi dung da co store chua
    $store1 = Store::where('ownId', $user->id)->first();
    if (!$store1) {
        return response()->json([
            'message' => "User has not yet registered a store.."
        ], 500);
    }

    //Neu nguoi dung da dang nhap va da co store thi kiem tra du lieu dau vao


    // Kiem tra du lieu dau vao
    $validator = Validator::make($request->all(), [
        //Dieu kien ten cua hang la duy nhat, khong trungrung
        'storeName'   => [
            'nullable',
            'max:255',
            Rule::unique('store', 'storeName')->ignore($store1->id)
        ],
        'description' => 'nullable',
          //  'avatar'      => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048', // Validate file ảnh
        'avatar'      => 'nullable'
    ]);

    if ($validator->fails()){
        return response()->json([
            'status'  => 422,
            'message' => $validator->messages()
        ], 422);
    } 

    // Cap nhat thong tin neu du lieu khong rongrong
    if ($request->has('storeName') && trim($request->storeName) !== '') {
        $store1->storeName = $request->storeName;
    }
    if ($request->has('description') && trim($request->description) !== '') {
        $store1->description = $request->description;
    }
    if ($request->has('avatar') && trim($request->avatar) !== '') {
        $store1->avatar = $request->avatar;
    }

    $store1->save();

    return response()->json([
        "status"  => 200,
        "message" => "Successfully updated store profile",
        "store"   => $store1
    ], 200);
}


//Tim kiem store by id
public function findStoreById(int $store_id)
{
    $store = Store::find($store_id);
    if (!$store) {
        return response()->json([
            "status" => 404,
            "message" =>"Store with id = $store_id not found"
        ],404);
    }
    
    return  response()->json([
        "status"=> 200,
        "store" => $store
    ]);

}

//Tim kiem store theo id nguoi so huu
public function findStoreByOwnId(int $user_id)
{
    $store = Store::where('ownId', $user_id)->first();
    if (!$store) {
        return response()->json([
            "status" => 404,
            "message" =>"Store with ownId = $user_id not found"
        ],404);
    }
    
    return response()->json([
        "status"=> 200,
        "store" => $store
    ]);

}


//Nguoi dung xoa store cua ban than
public function deleteStore(Request $request)
{
    //Kiem tra trang thai dang nhap cua nguoi dung
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'message' => 'User not authenticated.',
        ], 404);
    }
    $store = Store::where('ownId',$user->id)->first();

    if (!$store) {
        return response()->json([
            "status" => 404,
            "message" => "Store not found"
        ], 404);
    }
    
    // Kiểm tra quyền sở hữu: chỉ cho phép chủ cửa hàng được xóa
    
    $store->delete();
    
    return response()->json([
        "status"  => 200,
        "message" => "Store deleted successfully"
    ], 200);
}

//Nguoi dung truy cap store cua ban than

public function myStore(Request $request)
{
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            "status"=> 401,
            "message"=> "User not authenticated."
            ], 401);
    }
    $store = Store::where("ownId",$user->id)->first();
    if (!$store) {
        return response()->json([
            "status"=> 404,
            "message"=> "Store not found"
            ], 404);
    }

    return response()->json([
        "status"=> 200,
        "store" => $store
        ]);
    


}




    
}
     
    


   