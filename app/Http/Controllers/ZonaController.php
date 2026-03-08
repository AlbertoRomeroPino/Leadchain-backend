<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ZonaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Zona::all());
    }

    public function show(Zona $zona): JsonResponse
    {
        return response()->json($zona);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre_zona' => 'required|string|max:100',
            'poligono_coordenadas' => 'required',
        ]);

        $zona = Zona::create($validated);

        return response()->json($zona, 201);
    }

    public function update(Request $request, Zona $zona): JsonResponse
    {
        $validated = $request->validate([
            'nombre_zona' => 'sometimes|string|max:100',
            'poligono_coordenadas' => 'sometimes',
        ]);

        $zona->update($validated);

        return response()->json($zona);
    }

    public function destroy(Zona $zona): JsonResponse
    {
        $zona->delete();

        return response()->json(null, 204);
    }
}
