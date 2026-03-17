<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ClienteController extends Controller
{
    #[OA\Get(
        path: '/api/clientes',
        tags: ['Clientes', 'Listado', 'clientes', 'cliente'],
        summary: 'Listar todos los clientes',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Listado de clientes', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/ClienteResource'))),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(ClienteResource::collection(Cliente::with('usuarioAsignado')->get()));
    }

    #[OA\Get(
        path: '/api/clientes/{cliente}',
        tags: ['Clientes', 'Detalle', 'clientes', 'cliente'],
        summary: 'Obtener un cliente por ID',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'cliente', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Cliente encontrado', content: new OA\JsonContent(ref: '#/components/schemas/ClienteResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'Cliente no encontrado'),
        ]
    )]
    public function show(Cliente $cliente): JsonResponse
    {
        return response()->json(new ClienteResource($cliente->load(['usuarioAsignado', 'edificios', 'visitas'])));
    }

    #[OA\Post(
        path: '/api/clientes',
        tags: ['Clientes', 'Creación', 'clientes', 'cliente'],
        summary: 'Crear un nuevo cliente',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', example: 'Antonio'),
                    new OA\Property(property: 'apellidos', type: 'string', example: 'Perez Garcia'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'antonio@ejemplo.com'),
                    new OA\Property(property: 'telefono', type: 'string', example: '612345678'),
                    new OA\Property(property: 'id_usuario_asignado', type: 'integer', example: 2),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Cliente creado', content: new OA\JsonContent(ref: '#/components/schemas/ClienteResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(ClienteRequest $request): JsonResponse
    {
        $cliente = Cliente::create($request->validated());

        return response()->json(new ClienteResource($cliente), 201);
    }

    #[OA\Put(
        path: '/api/clientes/{cliente}',
        tags: ['Clientes', 'Actualización', 'clientes', 'cliente'],
        summary: 'Actualizar un cliente completo',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'cliente', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'Cliente actualizado', content: new OA\JsonContent(ref: '#/components/schemas/ClienteResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    #[OA\Patch(
        path: '/api/clientes/{cliente}',
        tags: ['Clientes', 'Actualización', 'clientes', 'cliente'],
        summary: 'Actualizar parcialmente un cliente',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'cliente', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'Cliente actualizado', content: new OA\JsonContent(ref: '#/components/schemas/ClienteResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(ClienteRequest $request, Cliente $cliente): JsonResponse
    {
        $cliente->update($request->validated());

        return response()->json(new ClienteResource($cliente));
    }

    #[OA\Delete(
        path: '/api/clientes/{cliente}',
        tags: ['Clientes', 'Eliminación', 'clientes', 'cliente'],
        summary: 'Eliminar un cliente',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'cliente', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Cliente eliminado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
        ]
    )]
    public function destroy(Cliente $cliente): JsonResponse
    {
        $cliente->delete();

        return response()->json(null, 204);
    }
}
