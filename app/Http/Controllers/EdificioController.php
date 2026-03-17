<?php

namespace App\Http\Controllers;

use App\Http\Requests\EdificioRequest;
use App\Http\Resources\EdificioResource;
use App\Models\Edificio;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class EdificioController extends Controller
{
    #[OA\Get(
        path: '/api/edificios',
        tags: ['Edificios'],
        summary: 'Listar todos los edificios',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Listado de edificios', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/EdificioResource'))),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(EdificioResource::collection(Edificio::with(['zona', 'cliente'])->get()));
    }

    #[OA\Get(
        path: '/api/edificios/{edificio}',
        tags: ['Edificios'],
        summary: 'Obtener un edificio por ID',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'edificio', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Edificio encontrado', content: new OA\JsonContent(ref: '#/components/schemas/EdificioResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'Edificio no encontrado'),
        ]
    )]
    public function show(Edificio $edificio): JsonResponse
    {
        return response()->json(new EdificioResource($edificio->load(['zona', 'cliente'])));
    }

    #[OA\Post(
        path: '/api/edificios',
        tags: ['Edificios'],
        summary: 'Crear un nuevo edificio',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 201, description: 'Edificio creado', content: new OA\JsonContent(ref: '#/components/schemas/EdificioResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
            new OA\Response(response: 500, description: 'Error al crear el edificio'),
        ]
    )]
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

    #[OA\Put(
        path: '/api/edificios/{edificio}',
        tags: ['Edificios'],
        summary: 'Actualizar un edificio completo',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'edificio', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'Edificio actualizado', content: new OA\JsonContent(ref: '#/components/schemas/EdificioResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    #[OA\Patch(
        path: '/api/edificios/{edificio}',
        tags: ['Edificios'],
        summary: 'Actualizar parcialmente un edificio',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'edificio', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'Edificio actualizado', content: new OA\JsonContent(ref: '#/components/schemas/EdificioResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
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

    #[OA\Delete(
        path: '/api/edificios/{edificio}',
        tags: ['Edificios'],
        summary: 'Eliminar un edificio',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'edificio', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Edificio eliminado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
        ]
    )]
    public function destroy(Edificio $edificio): JsonResponse
    {
        $edificio->delete();

        return response()->json(null, 204);
    }
}
