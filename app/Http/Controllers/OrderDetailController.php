<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    public function index()
    {
        return response()->json(OrderDetail::all());
    }

    public function show($order_id, $product_id)
    {
        $orderDetail = OrderDetail::where('order_id', $order_id)->where('product_id', $product_id)->first();
        if (!$orderDetail) {
            return response()->json(['message' => 'Order Detail not found'], 404);
        }
        return response()->json($orderDetail);
    }

    public function store(Request $request)
    {
        $orderDetail = OrderDetail::create($request->all());
        return response()->json($orderDetail, 201);
    }

    public function update(Request $request, $order_id, $product_id)
    {
        $orderDetail = OrderDetail::where('order_id', $order_id)->where('product_id', $product_id)->first();
        if (!$orderDetail) {
            return response()->json(['message' => 'Order Detail not found'], 404);
        }
        $orderDetail->update($request->all());
        return response()->json($orderDetail);
    }

    public function destroy($order_id, $product_id)
    {
        $orderDetail = OrderDetail::where('order_id', $order_id)->where('product_id', $product_id)->first();
        if (!$orderDetail) {
            return response()->json(['message' => 'Order Detail not found'], 404);
        }
        $orderDetail->delete();
        return response()->json(['message' => 'Order Detail deleted']);
    }
}
