<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZonaRequest;
use App\Models\Zona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function store(ZonaRequest $request): JsonResponse
    {

        $zona = Zona::create(['nombre_zona' => $request['nombre_zona']]);

        DB::statement("
            UPDATE zonas SET
                esquina_noroeste = ST_SetSRID(ST_MakePoint(?, ?), 4326),
                esquina_noreste  = ST_SetSRID(ST_MakePoint(?, ?), 4326),
                esquina_suroeste = ST_SetSRID(ST_MakePoint(?, ?), 4326),
                esquina_sureste  = ST_SetSRID(ST_MakePoint(?, ?), 4326)
            WHERE id = ?
        ", [
            $request['esquina_noroeste']['lng'], $request['esquina_noroeste']['lat'],
            $request['esquina_noreste']['lng'], $request['esquina_noreste']['lat'],
            $request['esquina_suroeste']['lng'], $request['esquina_suroeste']['lat'],
            $request['esquina_sureste']['lng'], $request['esquina_sureste']['lat'],
            $zona->id,
        ]);

        return response()->json($zona->fresh(), 201);
    }

    public function update(ZonaRequest $request, Zona $zona): JsonResponse
    {

        if (isset($validated['nombre_zona'])) {
            $zona->update(['nombre_zona' => $validated['nombre_zona']]);
        }

        $esquinas = ['esquina_noroeste', 'esquina_noreste', 'esquina_suroeste', 'esquina_sureste'];
        foreach ($esquinas as $esquina) {
            if (isset($validated[$esquina])) {
                DB::statement(
                    "UPDATE zonas SET {$esquina} = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE id = ?",
                    [$request[$esquina]['lng'], $request[$esquina]['lat'], $zona->id]
                );
            }
        }

        return response()->json($zona->fresh());
    }

    public function destroy(Zona $zona): JsonResponse
    {
        $zona->delete();

        return response()->json(null, 204);
    }
}
