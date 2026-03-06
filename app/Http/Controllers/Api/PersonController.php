<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index()
    {
        $people = Person::with(['employee', 'agent', 'user'])->paginate(10);

        return response()->json($people);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:people,email',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean'
        ]);

        $person = Person::create($data);

        return response()->json($person, 201);
    }


    public function show(Person $person)
    {
        return response()->json(
            $person->load(['employee', 'agent', 'user'])
        );
    }


    public function update(Request $request, Person $person)
    {
        $data = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:people,email,' . $person->id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean'
        ]);

        $person->update($data);

        return response()->json($person);
    }


    public function destroy(Person $person)
    {
        $person->delete();

        return response()->json([
            'message' => 'Person deleted'
        ]);
    }
}
