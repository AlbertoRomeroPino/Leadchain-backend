<?php

namespace App\Http\Controllers;

use App\Models\Edificio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EdificioController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Edificio::with(['zona', 'cliente'])->get());
    }

    public function show(Edificio $edificio): JsonResponse
    {
        return response()->json($edificio->load(['zona', 'cliente']));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'direccion_completa' => 'required|string|max:255',
            'planta' => 'nullable|string|max:20',
            'puerta' => 'nullable|string|max:10',
            'ubicacion' => 'required',
            'id_zona' => 'required|exists:zonas,id',
            'tipo' => 'required|string|max:50',
            'id_cliente' => 'nullable|exists:clientes,id',
        ]);

        $edificio = Edificio::create($validated);

        return response()->json($edificio, 201);
    }

    public function update(Request $request, Edificio $edificio): JsonResponse
    {
        $validated = $request->validate([
            'direccion_completa' => 'sometimes|string|max:255',
            'planta' => 'nullable|string|max:20',
            'puerta' => 'nullable|string|max:10',
            'ubicacion' => 'sometimes',
            'id_zona' => 'sometimes|exists:zonas,id',
            'tipo' => 'sometimes|string|max:50',
            'id_cliente' => 'nullable|exists:clientes,id',
        ]);

        $edificio->update($validated);

        return response()->json($edificio);
    }

    public function destroy(Edificio $edificio): JsonResponse
    {
        $edificio->delete();

        return response()->json(null, 204);
    }
}
