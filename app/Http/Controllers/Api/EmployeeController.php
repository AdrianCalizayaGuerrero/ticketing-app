<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
public function index()
    {
        $employees = Employee::with('person')->paginate(10);

        return response()->json($employees);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'person_id' => 'required|exists:people,id',
            'department' => 'required|string|max:255',
            'position' => 'nullable|string|max:255'
        ]);

        $employee = Employee::create([
            'id' => $data['person_id'],
            'department' => $data['department'],
            'position' => $data['position']
        ]);

        return response()->json(
            $employee->load('person'),
            201
        );
    }


    public function show(Employee $employee)
    {
        return response()->json(
            $employee->load(['person', 'reportedTickets'])
        );
    }


    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'department' => 'sometimes|string|max:255',
            'position' => 'nullable|string|max:255'
        ]);

        $employee->update($data);

        return response()->json(
            $employee->load('person')
        );
    }


    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json([
            'message' => 'Employee removed'
        ]);
    }
}
