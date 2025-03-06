<?php

namespace App\Http\Controllers;

use App\Models\ImageDetail;
use Illuminate\Http\Request;

class ImageDetailController extends Controller
{
    public function index()
    {
        return response()->json(ImageDetail::all());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'imageUrl' => 'required|string',
            'product_id' => 'required|integer|exists:product,id'
        ]);

        $image = ImageDetail::create($validatedData);
        return response()->json($image, 201);
    }

    public function show($id)
    {
        $image = ImageDetail::find($id);
        return $image ? response()->json($image) : response()->json(['message' => 'Not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $image = ImageDetail::find($id);
        if (!$image) return response()->json(['message' => 'Not found'], 404);

        $image->update($request->all());
        return response()->json($image);
    }

    public function destroy($id)
    {
        $image = ImageDetail::find($id);
        if (!$image) return response()->json(['message' => 'Not found'], 404);

        $image->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
