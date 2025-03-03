<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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


public function update_profile(Request $request, int $user_id)
{
    // Kiem tra cua hang cua nguoi dung
    $store = Store::where('ownId', $user_id)->first();
    if (!$store) {
        return response()->json([
            "status"  => 500,
            "message" => "User has not yet registered a store."
        ], 500);
    }

    // Kiem tra du lieu dau vao
    $validator = Validator::make($request->all(), [
        //Dieu kien ten cua hang la duy nhat, khong trungrung
        'storeName'   => [
            'nullable',
            'max:255',
            Rule::unique('store', 'storeName')->ignore($store->id)
        ],
        'description' => 'nullable',
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
        $store->storeName = $request->storeName;
    }
    if ($request->has('description') && trim($request->description) !== '') {
        $store->description = $request->description;
    }
    if ($request->has('avatar') && trim($request->avatar) !== '') {
        $store->avatar = $request->avatar;
    }

    $store->save();

    return response()->json([
        "status"  => 200,
        "message" => "Successfully updated store profile",
        "store"   => $store
    ], 200);
}

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
// public function deleteStore(int $store_id)
// {
//     $store = Store::find($store_id);
    
//     if (!$store) {
//         return response()->json([
//             "status" => 404,
//             "message" => "Store not found"
//         ], 404);
//     }
    
//     // Kiểm tra quyền sở hữu: chỉ cho phép chủ cửa hàng được xóa
//     if ($store->ownId !== auth()->id()) {
//         return response()->json([
//             "status"  => 403,
//             "message" => "You are not authorized to delete this store."
//         ], 403);
//     }
    
//     $store->delete();
    
//     return response()->json([
//         "status"  => 200,
//         "message" => "Store deleted successfully"
//     ], 200);
// }




    
}
     
    


   