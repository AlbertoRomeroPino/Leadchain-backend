<?php

namespace App\Http\Controllers;

use App\Models\EstadoVisita;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstadoVisitaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(EstadoVisita::all());
    }

    public function show(EstadoVisita $estadoVisita): JsonResponse
    {
        return response()->json($estadoVisita);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'etiqueta' => 'required|string|max:50',
            'color_hex' => 'required|string|max:7',
        ]);

        $estadoVisita = EstadoVisita::create($validated);

        return response()->json($estadoVisita, 201);
    }

    public function update(Request $request, EstadoVisita $estadoVisita): JsonResponse
    {
        $validated = $request->validate([
            'etiqueta' => 'sometimes|string|max:50',
            'color_hex' => 'sometimes|string|max:7',
        ]);

        $estadoVisita->update($validated);

        return response()->json($estadoVisita);
    }

    public function destroy(EstadoVisita $estadoVisita): JsonResponse
    {
        $estadoVisita->delete();

        return response()->json(null, 204);
    }
}
