<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(User::with(['zona', 'responsable'])->get());
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user->load(['zona', 'responsable', 'subordinados', 'clientes']));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:150',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'rol' => 'required|string|max:50',
            'id_responsable' => 'nullable|exists:users,id',
            'id_zona' => 'nullable|exists:zonas,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'apellidos' => 'sometimes|string|max:150',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'rol' => 'sometimes|string|max:50',
            'id_responsable' => 'nullable|exists:users,id',
            'id_zona' => 'nullable|exists:zonas,id',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
