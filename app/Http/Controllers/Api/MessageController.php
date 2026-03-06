<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // GET /api/messages
    public function index()
    {
        $messages = Message::with(['ticket', 'author'])->get();
        return response()->json($messages);
    }

    // POST /api/messages
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'is_internal' => 'boolean',
            'ticket_id' => 'required|exists:tickets,id',
            'author_id' => 'required|exists:users,id'
        ]);

        $message = Message::create($request->all());

        return response()->json($message, 201);
    }

    // GET /api/messages/{id}
    public function show($id)
    {
        $message = Message::with(['ticket', 'author'])->findOrFail($id);

        return response()->json($message);
    }

    // PUT /api/messages/{id}
    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);

        $request->validate([
            'content' => 'sometimes|string',
            'is_internal' => 'boolean'
        ]);

        $message->update($request->all());

        return response()->json($message);
    }

    // DELETE /api/messages/{id}
    public function destroy($id)
    {
        $message = Message::findOrFail($id);

        $message->delete();

        return response()->json([
            'message' => 'Message deleted successfully'
        ]);
    }
}
