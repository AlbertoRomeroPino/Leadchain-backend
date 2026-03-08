<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Cliente::with('usuarioAsignado')->get());
    }

    public function show(Cliente $cliente): JsonResponse
    {
        return response()->json($cliente->load(['usuarioAsignado', 'edificios', 'visitas']));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'id_usuario_asignado' => 'required|exists:users,id',
        ]);

        $cliente = Cliente::create($validated);

        return response()->json($cliente, 201);
    }

    public function update(Request $request, Cliente $cliente): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'apellidos' => 'sometimes|string|max:150',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'id_usuario_asignado' => 'sometimes|exists:users,id',
        ]);

        $cliente->update($validated);

        return response()->json($cliente);
    }

    public function destroy(Cliente $cliente): JsonResponse
    {
        $cliente->delete();

        return response()->json(null, 204);
    }
}
