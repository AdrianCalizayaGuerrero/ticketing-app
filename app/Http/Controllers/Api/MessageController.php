<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        return response()->json(Message::with(['author.person'])->get());
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'content'     => 'required|string',
            'is_internal' => 'boolean',
            'ticked_id'   => 'required|exists:tickeds,id',
        ]);

        $data['author_id'] = $user->id;

        $message = Message::create($data);

        return response()->json($message->load('author.person'), 201);
    }

    public function show($id)
    {
        return response()->json(Message::with(['author.person'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);
        $message->update($request->validate(['content' => 'required|string']));
        return response()->json($message);
    }

    public function destroy($id)
    {
        Message::findOrFail($id)->delete();
        return response()->json(['message' => 'Mensaje eliminado']);
    }
}
