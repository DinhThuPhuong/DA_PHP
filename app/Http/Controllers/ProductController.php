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
        if ($category_id == 0) {
            $products = Product::where('productName', 'like', '%' . $keyword . '%')->get();
        }
        //Neu category id khac 0 co nghia la tim kiem san pham theo danh muc
        else {
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

        return response($product, 200);
    }
    public function createProduct(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'productName' => 'required|string|max:255',
            'remainQuantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'store_id' => 'required|exists:store,id',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'imageDetails' => 'required|array|min:2',
            'imageDetails.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'productDetail' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Kiểm tra store_id có tồn tại không
        $store = Store::find($request->store_id);
        if (!$store) {
            return response()->json(['error' => 'Store không tồn tại'], 404);
        }

        // Upload ảnh thumbnail lên Cloudinary
        $thumbnailUpload = $this->cloudinary->upload($request->file('thumbnail'));
        $thumbnailUrl = $thumbnailUpload['secure_url'] ?? null;

        if (!$thumbnailUrl) {
            return response()->json(['error' => 'Không thể tải ảnh thumbnail lên'], 500);
        }
        DB::beginTransaction();
        try {
            // Tạo sản phẩm mới
            $product = Product::create([
                'category_id' => $request->category_id,
                'productName' => $request->productName,
                'remainQuantity' => $request->remainQuantity,
                'price' => $request->price,
                'store_id' => $request->store_id,
                'thumbnail' => $thumbnailUrl,
                'isValidated' => false,
                'soldQuantity' => 0,
                'productDetail' => $request->productDetail,
            ]);

            // Upload ảnh chi tiết và lưu vào database
            foreach ($request->file('imageDetails') as $image) {
                $imageUpload = $this->cloudinary->upload($image);
                if (!$imageUpload['secure_url']) throw new \Exception('Upload ảnh chi tiết thất bại');

                ImageDetail::create([
                    'imageUrl' => $imageUpload['secure_url'],
                    'product_id' => $product->id,
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Sản phẩm được tạo thành công', 'product' => $product], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Tạo sản phẩm thất bại: ' . $e->getMessage()], 500);
        }
    }

    // Cập nhật sản phẩm
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'exists:categories,id',
            'productName' => 'string|max:255',
            'remainQuantity' => 'integer|min:0',
            'price' => 'numeric|min:0',
            'store_id' => 'exists:stores,id',
            'thumbnail' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'imageDetails' => 'array|min:2',
            'imageDetails.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'productDetail' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Nếu có ảnh mới, upload lên Cloudinary
        if ($request->hasFile('thumbnail')) {
            $thumbnailUpload = $this->cloudinary->upload($request->file('thumbnail'));
            $product->thumnail = $thumbnailUpload['secure_url'] ?? $product->thumnail;
        }

        // Cập nhật thông tin sản phẩm
        $product->update($request->only([
            'category_id',
            'productName',
            'remainQuantity',
            'price',
            'store_id',
            'productDetail'
        ]));

        // Nếu có ảnh chi tiết mới, thêm vào database
        if ($request->hasFile('imageDetails')) {
            foreach ($request->file('imageDetails') as $image) {
                $imageUpload = $this->cloudinary->upload($image);
                $imageUrl = $imageUpload['secure_url'] ?? null;

                if ($imageUrl) {
                    ImageDetail::create([
                        'imageUrl' => $imageUrl,
                        'product_id' => $product->id,
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Sản phẩm được cập nhật thành công',
            'product' => $product,
        ], 200);
    }

    // Xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
        }

        // Xóa ảnh chi tiết
        ImageDetail::where('product_id', $id)->delete();

        // Xóa sản phẩm
        $product->delete();

        return response()->json(['message' => 'Sản phẩm đã được xóa'], 200);
    }
}
