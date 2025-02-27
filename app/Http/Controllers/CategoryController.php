<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isEmpty;

class CategoryController extends Controller
{

    //Tao moi category
    public function index()
    {
        
        $category = Category::all();
        $data = [
            'status'=>200,
            'category' => $category
        ];
        return response()->json($data, 200);

    }

    //Tao categorycategory
    public function create(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'categoryName' => 'required',
    ]);

    if ($validator->fails()) {
        $data = [
            'status' => 422,
            'message' => $validator->messages()
        ];
        return response()->json($data, 422);
    } else {
        $category = new Category;
        $category->categoryName = $request->categoryName;
        $category->save();

        $data = [
            'status'   => 200,
            'message'  => 'Successfully create new category',
            'category' => $category,  // Trả về đối tượng Category vừa lưu
        ];

        return response()->json($data, 200);
    }
}

//Chinh sua categorycategory
public function update(Request $request, int $id)
{
    //Kiem tra du lieu hop lele
    $validator = Validator::make($request->all(), [
        'categoryName' => 'required',
    ]);

    if ($validator->fails()) {
        $data = [
            'status' => 422,
            'message' => $validator->messages()
        ];
        return response()->json($data, 422);
    } else {

        //Tim kiem category theo id dc truyen vaovao
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
            ], 404);
        }

        //Gan gia tri moi cho thuoc tinh namename
        $category->categoryName = $request->categoryName;

        //Luu vao csdlcsdl
        $category->save();

        //Tao noi dung thong bao de returnreturn
        $data = [
            'status'   => 200,
            'message'  => "Successfully update category with id = $id",
            'category' => $category,  // Trả về đối tượng Category mới cập nhật
        ];

        return response()->json($data, 200);
    }

    }


    //Xoa categorycategory
    public function delete(int $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message'=> "Category with $id not found",
            ], 404);
        }
        $category->delete();

        $data = [
            "status"=> 200,
            "message"=> "Successfully deleted category"
        ];

        return response()->json($data, 200);
        
    }


}