<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitaRequest;
use App\Http\Resources\VisitaResource;
use App\Models\Visita;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class VisitaController extends Controller
{
    #[OA\Get(
        path: '/api/visitas',
        tags: ['Visitas'],
        summary: 'Listar todas las visitas',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Listado de visitas', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/VisitaResource'))),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(VisitaResource::collection(Visita::with(['usuario', 'cliente', 'estado'])->get()));
    }

    #[OA\Get(
        path: '/api/visitas/{visita}',
        tags: ['Visitas'],
        summary: 'Obtener una visita por ID',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'visita', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Visita encontrada', content: new OA\JsonContent(ref: '#/components/schemas/VisitaResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'Visita no encontrada'),
        ]
    )]
    public function show(Visita $visita): JsonResponse
    {
        return response()->json(new VisitaResource($visita->load(['usuario', 'cliente', 'estado'])));
    }

    #[OA\Post(
        path: '/api/visitas',
        tags: ['Visitas'],
        summary: 'Crear una nueva visita',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 201, description: 'Visita creada', content: new OA\JsonContent(ref: '#/components/schemas/VisitaResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(VisitaRequest $request): JsonResponse
    {
        $visita = Visita::create($request->validated());

        return response()->json(new VisitaResource($visita), 201);
    }

    #[OA\Put(
        path: '/api/visitas/{visita}',
        tags: ['Visitas'],
        summary: 'Actualizar una visita completa',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'visita', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'Visita actualizada', content: new OA\JsonContent(ref: '#/components/schemas/VisitaResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    #[OA\Patch(
        path: '/api/visitas/{visita}',
        tags: ['Visitas'],
        summary: 'Actualizar parcialmente una visita',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'visita', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'Visita actualizada', content: new OA\JsonContent(ref: '#/components/schemas/VisitaResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(VisitaRequest $request, Visita $visita): JsonResponse
    {
        $visita->update($request->validated());

        return response()->json(new VisitaResource($visita));
    }

    #[OA\Delete(
        path: '/api/visitas/{visita}',
        tags: ['Visitas'],
        summary: 'Eliminar una visita',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'visita', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Visita eliminada'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
        ]
    )]
    public function destroy(Visita $visita): JsonResponse
    {
        $visita->delete();

        return response()->json(null, 204);
    }
}
