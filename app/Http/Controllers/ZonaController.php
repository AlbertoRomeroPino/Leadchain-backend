<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZonaRequest;
use App\Http\Resources\ZonaResource;
use App\Models\Zona;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class ZonaController extends Controller
{
    #[OA\Get(
        path: '/api/zonas',
        tags: ['Zonas'],
        summary: 'Listar todas las zonas',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Listado de zonas', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/ZonaResource'))),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(ZonaResource::collection(Zona::all()));
    }

    #[OA\Get(
        path: '/api/zonas/{zona}',
        tags: ['Zonas'],
        summary: 'Obtener una zona por ID',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'zona', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Zona encontrada', content: new OA\JsonContent(ref: '#/components/schemas/ZonaResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'Zona no encontrada'),
        ]
    )]
    public function show(Zona $zona): JsonResponse
    {
        return response()->json(new ZonaResource($zona));
    }

    #[OA\Post(
        path: '/api/zonas',
        tags: ['Zonas'],
        summary: 'Crear una nueva zona',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ZonaInput')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Zona creada', content: new OA\JsonContent(ref: '#/components/schemas/ZonaResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
            new OA\Response(response: 500, description: 'Error al crear la zona'),
        ]
    )]
    public function store(ZonaRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $zona = Zona::create(['nombre_zona' => $request['nombre_zona']]);

            DB::statement(
                "
            UPDATE zonas SET
                esquina_noroeste = ST_SetSRID(ST_MakePoint(?, ?), 4326),
                esquina_noreste  = ST_SetSRID(ST_MakePoint(?, ?), 4326),
                esquina_suroeste = ST_SetSRID(ST_MakePoint(?, ?), 4326),
                esquina_sureste  = ST_SetSRID(ST_MakePoint(?, ?), 4326)
            WHERE id = ?
        ",
                [
                    $request['esquina_noroeste']['lng'],
                    $request['esquina_noroeste']['lat'],
                    $request['esquina_noreste']['lng'],
                    $request['esquina_noreste']['lat'],
                    $request['esquina_suroeste']['lng'],
                    $request['esquina_suroeste']['lat'],
                    $request['esquina_sureste']['lng'],
                    $request['esquina_sureste']['lat'],
                    $zona->id,
                ]
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error al crear la zona', 'message' => $e->getMessage()], 500);
        }

        return response()->json(new ZonaResource($zona->fresh()), 201);
    }

    #[OA\Put(
        path: '/api/zonas/{zona}',
        tags: ['Zonas'],
        summary: 'Actualizar una zona completa',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'zona', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ZonaInput')),
        responses: [
            new OA\Response(response: 200, description: 'Zona actualizada', content: new OA\JsonContent(ref: '#/components/schemas/ZonaResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    #[OA\Patch(
        path: '/api/zonas/{zona}',
        tags: ['Zonas'],
        summary: 'Actualizar parcialmente una zona',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'zona', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'Zona actualizada', content: new OA\JsonContent(ref: '#/components/schemas/ZonaResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
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

    #[OA\Delete(
        path: '/api/zonas/{zona}',
        tags: ['Zonas'],
        summary: 'Eliminar una zona',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'zona', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Zona eliminada'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
        ]
    )]
    public function destroy(Zona $zona): JsonResponse
    {
        $zona->delete();
        return response()->json(null, 204);
    }
}
