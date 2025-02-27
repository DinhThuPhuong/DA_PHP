<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;




class StudentController extends Controller
{
    public function index()
    {
        // 
        // $student = Student::create([
        //     'name' => 'Thu Phuong',
        //     'email' => 'dinhthuphuong.it@gmail.com',
        //     'phone' =>'0347335846'
        // ]);
        $student = Student::all();
        $data = [
            'status'=>200,
            'student' => $student
        ];
        return response()->json($data, 200);

    }
    public function create(Request $request)
    {
        Log::info('StudentController@create được gọi', $request->all());
        $validator = Validator::make($request->all(),
        [
            'name' => 'required',
            'email'=> 'required|email',
            'phone' => 'required'
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

            //Khoi tao Student
            $student = new Student;

            //Gan du lieu cho cac thuoc tinh trong Student
            $student->name = $request->name;
            $student->email = $request->email;  
            $student->phone = $request->phone;

            //Luu vao co so du lieu
            $student->save();

            $data = [
                'status' =>200,
                'message' => 'Succesfully create new student'
            ];

            return response()->json($data,200);

        }


    }
}