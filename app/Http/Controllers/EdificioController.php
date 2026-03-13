<?php

namespace App\Http\Controllers;

use App\Http\Requests\EdificioRequest;
use App\Http\Resources\EdificioResource;
use App\Models\Edificio;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EdificioController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(EdificioResource::collection(Edificio::with(['zona', 'cliente'])->get()));
    }

    public function show(Edificio $edificio): JsonResponse
    {
        return response()->json(new EdificioResource($edificio->load(['zona', 'cliente'])));
    }

    public function store(EdificioRequest $request): JsonResponse
    {
        $data = $request->all();
        $ubicacion = $data['ubicacion'];

        try {
            DB::beginTransaction();

            $inserted = DB::selectOne(
                'INSERT INTO edificios (direccion_completa, planta, puerta, id_zona, tipo, id_cliente, ubicacion, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ST_SetSRID(ST_MakePoint(?, ?), 4326), NOW(), NOW())
                 RETURNING id',
                [
                    $data['direccion_completa'],
                    $data['planta'] ?? null,
                    $data['puerta'] ?? null,
                    $data['id_zona'],
                    $data['tipo'],
                    $data['id_cliente'] ?? null,
                    $ubicacion['lng'],
                    $ubicacion['lat'],
                ]
            );

            $edificio = Edificio::findOrFail($inserted->id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear el edificio', 'message' => $e->getMessage()], 500);
        }

        return response()->json(new EdificioResource($edificio->fresh()->load(['zona', 'cliente'])), 201);
    }

    public function update(EdificioRequest $request, Edificio $edificio): JsonResponse
    {
        $data = $request->all();

        try {
            DB::beginTransaction();

            $updatable = $request->only([
                'direccion_completa',
                'planta',
                'puerta',
                'id_zona',
                'tipo',
                'id_cliente',
            ]);

            if (!empty($updatable)) {
                $edificio->update($updatable);
            }

            if (isset($data['ubicacion'])) {
                DB::statement(
                    'UPDATE edificios SET ubicacion = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE id = ?',
                    [$data['ubicacion']['lng'], $data['ubicacion']['lat'], $edificio->id]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar el edificio', 'message' => $e->getMessage()], 500);
        }

        return response()->json(new EdificioResource($edificio->fresh()->load(['zona', 'cliente'])));
    }

    public function destroy(Edificio $edificio): JsonResponse
    {
        $edificio->delete();

        return response()->json(null, 204);
    }
}
