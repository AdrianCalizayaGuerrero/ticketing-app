<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('person')->get();

        return response()->json($users);
    }

    // Crear un nuevo usuario
    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_id' => 'required|exists:people,id',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    // Mostrar un usuario específico
    public function show($id)
    {
        $user = User::with('person')->findOrFail($id);

        return response()->json($user);
    }

    // Actualizar usuario
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'person_id' => 'sometimes|exists:people,id',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $id,
            'password' => 'sometimes|string|min:6'
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    // Eliminar usuario
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
