<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::with(['person', 'role:id,name'])->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:people,email',
            'phone'      => 'nullable|string|max:20',
            'username'   => 'required|string|max:255|unique:users,username',
            'password'   => 'required|string|min:6',
            'role_id'    => 'required|exists:roles,id',
        ]);

        $person = Person::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
        ]);

        $user = User::create([
            'person_id' => $person->id,
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
            'role_id'      => $request->role_id,
        ]);

        return response()->json($user->load(['person', 'role:id,name']), 201);
    }

    public function show($id)
    {
        return response()->json(User::with(['person', 'role:id,name'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'sometimes|string|max:255|unique:users,username,' . $id,
            'password' => 'sometimes|string|min:6',
            'role_id'     => 'sometimes|exists:roles,id',
        ]);

        $data = $request->only(['username', 'role_id']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json($user->load(['person', 'role:id,name']));
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'Usuario eliminado']);
    }
}
