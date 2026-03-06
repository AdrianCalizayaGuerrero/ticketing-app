<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticked;
use Illuminate\Http\Request;

class TickedController extends Controller
{
    public function index()
    {
        return response()->json(
            Ticked::with([
                'reporter',
                'assignedAgent',
                'category',
                'priority',
                'messages'
            ])->paginate(10)
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'reporter_id' => 'required|exists:employees,id',
            'category_id' => 'required|exists:categories,id',
            'priority_id' => 'required|exists:priorities,id'
        ]);

        $ticked = Ticked::create($data);

        return response()->json($ticked, 201);
    }

    public function show(Ticked $ticked)
    {
        return response()->json(
            $ticked->load([
                'reporter',
                'assignedAgent',
                'messages',
                'statusHistories'
            ])
        );
    }

    public function update(Request $request, Ticked $ticked)
    {
        $data = $request->validate([
            'subject' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|string',
            'assigned_agent_id' => 'nullable|exists:agents,id'
        ]);

        $ticked->update($data);

        return response()->json($ticked);
    }

    public function destroy(Ticked $ticked)
    {
        $ticked->delete();

        return response()->json([
            'message' => 'Ticked deleted'
        ]);
    }
}
