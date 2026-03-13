<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZonaRequest;
use App\Http\Resources\ZonaResource;
use App\Models\Zona;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ZonaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(ZonaResource::collection(Zona::all()));
    }

    public function show(Zona $zona): JsonResponse
    {
        return response()->json(new ZonaResource($zona));
    }

    public function store(ZonaRequest $request): JsonResponse
    {


        try {
            DB::beginTransaction();

            $zona = Zona::create(['nombre_zona' => $request['nombre_zona']]);

            DB::statement("
            UPDATE zonas SET
                esquina_noroeste = ST_SetSRID(ST_MakePoint(?, ?), 4326),
                esquina_noreste  = ST_SetSRID(ST_MakePoint(?, ?), 4326),
                esquina_suroeste = ST_SetSRID(ST_MakePoint(?, ?), 4326),
                esquina_sureste  = ST_SetSRID(ST_MakePoint(?, ?), 4326)
            WHERE id = ?
        ", [
                $request['esquina_noroeste']['lng'],
                $request['esquina_noroeste']['lat'],
                $request['esquina_noreste']['lng'],
                $request['esquina_noreste']['lat'],
                $request['esquina_suroeste']['lng'],
                $request['esquina_suroeste']['lat'],
                $request['esquina_sureste']['lng'],
                $request['esquina_sureste']['lat'],
                $zona->id,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error al crear la zona', 'message' => $e->getMessage()], 500);
        }


        return response()->json(new ZonaResource($zona->fresh()), 201);
    }

    public function update(ZonaRequest $request, Zona $zona): JsonResponse
    {

        if (isset($request['nombre_zona'])) {
            $zona->update(['nombre_zona' => $request['nombre_zona']]);
        }

        $esquinas = ['esquina_noroeste', 'esquina_noreste', 'esquina_suroeste', 'esquina_sureste'];
        foreach ($esquinas as $esquina) {
            if (isset($request[$esquina])) {
                DB::statement(
                    "UPDATE zonas SET {$esquina} = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE id = ?",
                    [$request[$esquina]['lng'], $request[$esquina]['lat'], $zona->id]
                );
            }
        }

        return response()->json(new ZonaResource($zona->fresh()));
    }

    public function destroy(Zona $zona): JsonResponse
    {
        $zona->delete();
        // No devuelve nada, solo un código 204 No Content
        return response()->json(null, 204);
    }
}
