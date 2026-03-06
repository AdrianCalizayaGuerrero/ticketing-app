<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Priority;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    public function index()
    {
        $priorities = Priority::orderBy('weight', 'desc')->paginate(10);

        return response()->json($priorities);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'level' => 'required|string|max:50|unique:priorities,level',
            'weight' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $priority = Priority::create($data);

        return response()->json($priority, 201);
    }


    public function show(Priority $priority)
    {
        return response()->json(
            $priority->load('tickets')
        );
    }


    public function update(Request $request, Priority $priority)
    {
        $data = $request->validate([
            'level' => 'sometimes|string|max:50|unique:priorities,level,' . $priority->id,
            'weight' => 'sometimes|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $priority->update($data);

        return response()->json($priority);
    }


    public function destroy(Priority $priority)
    {
        $priority->delete();

        return response()->json([
            'message' => 'Priority deleted'
        ]);
    }
}
