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
            // Coordenadas de las 4 esquinas de la zona
            'lat_noroeste' => 'required|numeric|between:-90,90',
            'lng_noroeste' => 'required|numeric|between:-180,180',
            'lat_noreste' => 'required|numeric|between:-90,90',
            'lng_noreste' => 'required|numeric|between:-180,180',
            'lat_suroeste' => 'required|numeric|between:-90,90',
            'lng_suroeste' => 'required|numeric|between:-180,180',
            'lat_sureste' => 'required|numeric|between:-90,90',
            'lng_sureste' => 'required|numeric|between:-180,180',
        ]);

        $zona = Zona::create($validated);

        return response()->json($zona, 201);
    }

    public function update(Request $request, Zona $zona): JsonResponse
    {
        $validated = $request->validate([
            'nombre_zona' => 'sometimes|string|max:100',
            // Coordenadas de las 4 esquinas de la zona
            'lat_noroeste' => 'sometimes|numeric|between:-90,90',
            'lng_noroeste' => 'sometimes|numeric|between:-180,180',
            'lat_noreste' => 'sometimes|numeric|between:-90,90',
            'lng_noreste' => 'sometimes|numeric|between:-180,180',
            'lat_suroeste' => 'sometimes|numeric|between:-90,90',
            'lng_suroeste' => 'sometimes|numeric|between:-180,180',
            'lat_sureste' => 'sometimes|numeric|between:-90,90',
            'lng_sureste' => 'sometimes|numeric|between:-180,180',
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
