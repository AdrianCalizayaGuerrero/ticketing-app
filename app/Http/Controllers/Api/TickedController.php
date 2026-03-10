<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticked;
use App\Models\StatusHistory;
use App\Enums\TickedStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TickedController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Ticked::with(['reporter', 'assignedAgent', 'category', 'priority']);

        if ($user->role->name === 'client') {
            $employeeId = $user->person?->employee?->id ?? $user->person_id;
            $query->where('reporter_id', $employeeId);
        } elseif ($user->role->name === 'support') {
            $agentId = $user->person?->agent?->id ?? $user->person_id;
            $query->where('assigned_agent_id', $agentId);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'subject'      => 'required|string|max:255',
            'description'  => 'required|string',
            'category_id'  => 'required|exists:categories,id',
            'priority_id'  => 'required|exists:priorities,id',
            'reporter_id'  => 'sometimes|exists:employees,id',
        ]);

        if (empty($data['reporter_id'])) {
            $data['reporter_id'] = $user->person?->employee?->id ?? $user->person_id;
        }

        $data['reference_code'] = 'TCK-' . str_pad(Ticked::count() + 1, 4, '0', STR_PAD_LEFT);
        $data['status'] = TickedStatus::OPEN->value;

        $ticked = Ticked::create($data);

        return response()->json($ticked->load(['category', 'priority']), 201);
    }

    public function show(Request $request, Ticked $ticked)
    {
        return response()->json(
            $ticked->load(['reporter', 'assignedAgent', 'messages.author.person', 'statusHistories', 'category', 'priority'])
        );
    }

    public function update(Request $request, Ticked $ticked)
    {
        $user = $request->user();

        $data = $request->validate([
            'subject'           => 'sometimes|string|max:255',
            'description'       => 'sometimes|string',
            'status'            => 'sometimes|string',
            'assigned_agent_id' => 'nullable|exists:agents,id',
            'priority_id'       => 'sometimes|exists:priorities,id',
            'category_id'       => 'sometimes|exists:categories,id',
        ]);

        $oldStatus = $ticked->status instanceof TickedStatus
            ? $ticked->status->value
            : $ticked->status;

        $ticked->update($data);
        if (isset($data['status']) && $data['status'] !== $oldStatus) {
            StatusHistory::create([
                'ticked_id'       => $ticked->id,
                'previous_status' => $oldStatus,
                'new_status'      => $data['status'],
                'changed_by'      => $user->person?->agent?->id ?? $user->person_id,
                'changed_at'      => now(),
                'comment'         => 'Cambio de estado via sistema',
            ]);
        }

        return response()->json($ticked->load(['reporter', 'assignedAgent', 'category', 'priority']));
    }

    public function destroy(Ticked $ticked, Request $request)
    {
        if ($request->user()->role->name !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $ticked->delete();
        return response()->json(['message' => 'Ticket eliminado']);
    }
}
