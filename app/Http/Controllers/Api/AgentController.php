<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index()
    {
        $agents = Agent::with('person')->paginate(10);

        return response()->json($agents);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'person_id' => 'required|exists:people,id',
            'employee_code' => 'required|string|unique:agents,employee_code',
            'is_available' => 'boolean'
        ]);

        $agent = Agent::create([
            'id' => $data['person_id'],
            'employee_code' => $data['employee_code'],
            'is_available' => $data['is_available'] ?? true
        ]);

        return response()->json(
            $agent->load('person'),
            201
        );
    }


    public function show(Agent $agent)
    {
        return response()->json(
            $agent->load(['person', 'assignedTickets'])
        );
    }


    public function update(Request $request, Agent $agent)
    {
        $data = $request->validate([
            'employee_code' => 'sometimes|string|unique:agents,employee_code,' . $agent->id,
            'is_available' => 'boolean'
        ]);

        $agent->update($data);

        return response()->json($agent->load('person'));
    }


    public function destroy(Agent $agent)
    {
        $agent->delete();

        return response()->json([
            'message' => 'Agent removed'
        ]);
    }
}
