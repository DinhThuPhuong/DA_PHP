<?php

namespace App\Http\Controllers;

use App\Models\StoreNotification;
use Illuminate\Http\Request;

class StoreNotificationController extends Controller
{
    public function index()
    {
        return response()->json(StoreNotification::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_id' => 'required|integer',
            'message'  => 'required|string',
            'type'     => 'required|string',
            'isRead'   => 'boolean',
            'user_id'  => 'nullable|integer',
        ]);

        return response()->json(StoreNotification::create($data), 201);
    }

    public function show($id)
    {
        return response()->json(StoreNotification::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $notification = StoreNotification::findOrFail($id);
        $notification->update($request->all());
        return response()->json($notification);
    }

    public function destroy($id)
    {
        return response()->json(['deleted' => StoreNotification::destroy($id)]);
    }
}
