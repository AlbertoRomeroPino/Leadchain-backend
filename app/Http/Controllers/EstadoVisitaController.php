<?php

namespace App\Http\Controllers;

use App\Http\Resources\EstadoVisitaResource;
use App\Models\EstadoVisita;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class EstadoVisitaController extends Controller
{
    #[OA\Get(
        path: '/api/estados-visita',
        tags: ['Estados de Visita'],
        summary: 'Listar todos los estados de visita',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de estados de visita',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'etiqueta', type: 'string'),
                            new OA\Property(property: 'color_hex', type: 'string'),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(EstadoVisitaResource::collection(EstadoVisita::all()));
    }

    #[OA\Get(
        path: '/api/estados-visita/{estadoVisita}',
        tags: ['Estados de Visita'],
        summary: 'Obtener un estado de visita por ID',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'estadoVisita', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Estado de visita encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'etiqueta', type: 'string'),
                        new OA\Property(property: 'color_hex', type: 'string'),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'Estado de visita no encontrado'),
        ]
    )]
    public function show(EstadoVisita $estadoVisita): JsonResponse
    {
        return response()->json(new EstadoVisitaResource($estadoVisita));
    }
}
