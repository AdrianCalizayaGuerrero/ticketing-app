<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::with(['person', 'person.agent', 'person.employee'])
            ->where('username', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        // Determinar rol real basado en relaciones
        $role = $user->role;
        if ($role === 'cliente') {
            if ($user->person?->agent) {
                $role = 'agente';
            } elseif ($user->person?->employee) {
                $role = 'soporte';
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'       => $user->id,
                'username' => $user->username,
                'role'     => $role,
                'name'     => $user->person
                    ? $user->person->first_name . ' ' . $user->person->last_name
                    : $user->username,
                'email'    => $user->person?->email,
                'person_id'=> $user->person_id,
                'agent_id' => $user->person?->agent?->id,
                'employee_id' => $user->person?->employee?->id,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load(['person', 'person.agent', 'person.employee']);
        $role = $user->role;
        if ($role === 'cliente') {
            if ($user->person?->agent)    $role = 'agente';
            elseif ($user->person?->employee) $role = 'soporte';
        }
        return response()->json([
            'id'          => $user->id,
            'username'    => $user->username,
            'role'        => $role,
            'name'        => $user->person
                ? $user->person->first_name . ' ' . $user->person->last_name
                : $user->username,
            'email'       => $user->person?->email,
            'person_id'   => $user->person_id,
            'agent_id'    => $user->person?->agent?->id,
            'employee_id' => $user->person?->employee?->id,
        ]);
    }
}
