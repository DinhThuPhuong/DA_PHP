<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    public function index()
    {
        return response()->json(UserNotification::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'  => 'required|integer',
            'message'  => 'required|string',
            'type'     => 'required|string',
            'isRead'   => 'boolean',
            'store_id' => 'nullable|integer',
        ]);

        return response()->json(UserNotification::create($data), 201);
    }

    public function show($id)
    {
        return response()->json(UserNotification::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $notification = UserNotification::findOrFail($id);
        $notification->update($request->all());
        return response()->json($notification);
    }

    public function destroy($id)
    {
        return response()->json(['deleted' => UserNotification::destroy($id)]);
    }
}