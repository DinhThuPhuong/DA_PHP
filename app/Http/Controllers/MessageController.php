<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        return response()->json(Message::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'   => 'required|integer',
            'store_id'  => 'required|integer',
            'content'   => 'required|string',
            'isRead'    => 'boolean',
            'senderType'=> 'required|string',
        ]);

        return response()->json(Message::create($data), 201);
    }

    public function show($id)
    {
        return response()->json(Message::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);
        $message->update($request->all());
        return response()->json($message);
    }

    public function destroy($id)
    {
        return response()->json(['deleted' => Message::destroy($id)]);
    }
}
