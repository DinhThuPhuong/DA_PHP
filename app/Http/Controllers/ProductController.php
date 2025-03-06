<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::with('images')->get());
    }

    public function store(Request $request)
    {
        //Log::info('Data received:', $request->all());
        $validatedData = $request->validate([
            'category_id' => 'required|integer',
            'productName' => 'required|string',
            'remainQuantity' => 'required|integer',
            'price' => 'required|numeric',
            'store_id' => 'required|integer',
            'thumnail' => 'nullable|string',
            'isValidated' => 'boolean',
            'soldQuantity' => 'integer',
            'productDetail' => 'nullable|string',
        ]);

        $product = Product::create($validatedData);
        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::with('images')->find($id);
        return $product ? response()->json($product) : response()->json(['message' => 'Not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Not found'], 404);

        $product->update($request->all());
        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Not found'], 404);

        $product->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
