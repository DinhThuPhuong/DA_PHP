<?php

namespace App\Http\Controllers;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $data = [
            'status'=>200,
            'roles' => $roles
        ];
        return response()->json($data, 200);

    }

    public function create (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roleName'=> 'required',
        ]);
        if ($validator->fails()) {
            $data = [
                'status' => 422,
                'message' => $validator->messages()
            ];
            return response()->json($data, 422);
        }
        
        $role = new Role();

        $role->roleName = $request->roleName;

        $role->save();

        return response()->json($role,200);
    }
    public function update(Request $request, int $role_id)
    {
        $role = Role::find($role_id);
        if(!$role)
        {
            return response()->json([
                'status'=>404,
                'message'=> "Role with id = $role_id not found"
            ],404);

        }
        $validator = Validator::make($request->all(), [
            "roleName" => "required"
            ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message'=> $validator->messages()
                ],422);
            }
        $role->roleName = $request->roleName;
        $role->save();

        return response()->json($role,200);

    }

    public function delete (int $role_id)
    {
        $role = Role::find($role_id);
        if(!$role)
        {
            return response()->json([
                'status'=> 404,
                'message'=> "Role with id = $role_id not found"
                ],404);        
        }

        $role->delete();

        $data = [
            'status' => 200,
            'message' =>'Succesfully deleted role'
        ];
        return response()->json($data,200);
        
    }



}