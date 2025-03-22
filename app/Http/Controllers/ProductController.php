<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Store;
use App\Models\ImageDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\CloudinaryAdapter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $cloudinary;

    public function __construct(CloudinaryAdapter $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    //Lay danh sach san pham
    public function getAllProduct()
    {
        $products = Product::all()->toArray();

        $data = [
            'status' => 200,
            
            'product' => $products
        ];
        // return response()->json($data, 200);
        return response()->json($products, 200); // Trả về mảng
    }

    //Tim kiem san pham theo tu khoa va danh muc
    public function searchProduct(Request $request)
    {
        $keyword = $request->keyword;
        $category_id = $request->category_id;
        //Neu category id = 0 co nghia la tim kiem tat ca san pham
        if($category_id == 0)
        {
            $products = Product::where('productName', 'like', '%' . $keyword . '%')->get();
        }
        //Neu category id khac 0 co nghia la tim kiem san pham theo danh muc
        else
        {
            $products = Product::where('productName', 'like', '%' . $keyword . '%')->where('category_id', $category_id)->get();
        }

        return response()->json($products->toArray(), 200);
    }

    //Chi tiet san pham theo id
    public function display(int $product_id)
    {
        $product = Product::with('imageDetails')->find($product_id);

        if (!$product) {
            return response()->json([
                'status'  => 404,
                'message' => "Product with id = $product_id not found"
            ], 404);
        }
    
        return response($product,200);
    }


    public function createProduct(Request $request)
    {
        
        $store = Store::where("ownId", Auth::user()->id)->first();
        //Neu nguoi dung chua dang ki store
        // if(!$store)
        // {
        //     return response()->json([
        //         "status"=> 500,
        //         "message"=> "User has not yet registered a store."
        //         ],500);

        // }

        //Nguoi dung da dang ki store, thuc hien cac buoc tiep theo
        //Kiem tra du lieu dau vao
        $validator = Validator::make($request->all(), [
           'category_id'    => 'required|exists:categories,id',
            'productName'    => 'required|string|max:255',
            'remainQuantity' => 'required|integer|min:0',
            'price'          => 'required|min:0',
            'thumnail'       => 'required|image|mimes:jpg,jpeg,png,gif,svg|max:5120',
            'productDetail'    => 'nullable',
            'other_images'   => 'required|array|min:2',
            'other_images.*' => 'image|mimes:jpg,jpeg,png,gif,svg|max:5120',
        ]);
        
        if ($validator->fails())
        {
            return response()->json([
                'status' => 422,
                'message'=> $validator->errors()->first()
                ],422);
        }

        $products = new Product();
        $products->category_id = $request->category_id;
        $products->store_id = $store->id;
        $products->productName = $request->productName;
        $products->price = $request->price;
        $products->productDetail = $request->productDetail;
        $products->remainQuantity = $request->remainQuantity;
        
        //Gan mac dinh khi khoi tao san pham
        $products->soldQuantity = 0;
        $products->isValidated = true;//0843939939

        try {
            // Upload thumbnail lên Cloudinary
            if ($request->hasFile('thumnail')) {
                $file = $request->file('thumnail');
                $uploadResult = $this->cloudinary->upload(
                    $file,
                    [
                        'folder' => 'products/thumbnails',
                        'public_id' => time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                    ]
                );
                $products->thumnail = $uploadResult['secure_url'];
            }

            $products->save();

            // Xử lý upload các ảnh khác và lưu vào bảng imagedetail
            if ($request->hasFile('other_images')) {
                foreach ($request->file('other_images') as $file) {
                    $uploadResult = $this->cloudinary->upload(
                        $file,
                        [
                            'folder' => 'products/details',
                            'public_id' => time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . Str::random(5),
                        ]
                    );
                    
                    $imageDetail = new ImageDetail();
                    $imageDetail->product_id = $products->id;
                    $imageDetail->imageUrl = $uploadResult['secure_url'];
                    $imageDetail->save();
                }
            }
            
            return response()->json([
                'status'  => 200,
                'message' => 'Product created successfully.',
                'product' => $products,
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
        }
    }
}