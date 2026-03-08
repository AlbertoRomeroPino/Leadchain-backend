<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Visita::with(['usuario', 'cliente', 'estado'])->get());
    }

    public function show(Visita $visita): JsonResponse
    {
        return response()->json($visita->load(['usuario', 'cliente', 'estado']));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_usuario' => 'required|exists:users,id',
            'id_cliente' => 'required|exists:clientes,id',
            'fecha_hora' => 'required|date',
            'hora_visita' => 'nullable|date_format:H:i:s',
            'id_estado' => 'required|exists:estados_visita,id',
            'observaciones' => 'nullable|string',
        ]);

        $visita = Visita::create($validated);

        return response()->json($visita, 201);
    }

    public function update(Request $request, Visita $visita): JsonResponse
    {
        $validated = $request->validate([
            'id_usuario' => 'sometimes|exists:users,id',
            'id_cliente' => 'sometimes|exists:clientes,id',
            'fecha_hora' => 'sometimes|date',
            'hora_visita' => 'nullable|date_format:H:i:s',
            'id_estado' => 'sometimes|exists:estados_visita,id',
            'observaciones' => 'nullable|string',
        ]);

        $visita->update($validated);

        return response()->json($visita);
    }

    public function destroy(Visita $visita): JsonResponse
    {
        $visita->delete();

        return response()->json(null, 204);
    }
}
