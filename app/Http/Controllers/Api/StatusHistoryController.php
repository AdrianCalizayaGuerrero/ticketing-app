<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StatusHistory;
use Illuminate\Http\Request;

class StatusHistoryController extends Controller
{
    // GET /api/status-histories
    public function index()
    {
        $history = StatusHistory::with(['ticket', 'agent'])->get();

        return response()->json($history);
    }

    // POST /api/status-histories
    public function store(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickeds,id',
            'previous_status' => 'required',
            'new_status' => 'required',
            'comment' => 'nullable|string',
            'changed_by' => 'required|exists:agents,id',
            'changed_at' => 'nullable|date'
        ]);

        $history = StatusHistory::create($request->all());

        return response()->json($history, 201);
    }

    // GET /api/status-histories/{id}
    public function show($id)
    {
        $history = StatusHistory::with(['ticket', 'agent'])->findOrFail($id);

        return response()->json($history);
    }

    // PUT /api/status-histories/{id}
    public function update(Request $request, $id)
    {
        $history = StatusHistory::findOrFail($id);

        $request->validate([
            'previous_status' => 'sometimes',
            'new_status' => 'sometimes',
            'comment' => 'nullable|string'
        ]);

        $history->update($request->all());

        return response()->json($history);
    }

    // DELETE /api/status-histories/{id}
    public function destroy($id)
    {
        $history = StatusHistory::findOrFail($id);

        $history->delete();

        return response()->json([
            'message' => 'Status history deleted successfully'
        ]);
    }
}
