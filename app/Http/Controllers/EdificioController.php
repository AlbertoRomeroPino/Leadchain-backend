<?php

namespace App\Http\Controllers;

use App\Http\Requests\EdificioRequest;
use App\Http\Resources\EdificioResource;
use App\Http\Resources\EdificioDetailResource;
use App\Models\Cliente;
use App\Models\Edificio;
use App\Models\Zona;
use Illuminate\Http\Request;
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
        return response()->json(EdificioResource::collection(Edificio::with(['zona', 'clientes'])->get()));
    }

    #[OA\Get(
        path: '/api/edificios/{edificio}/detalle',
        tags: ['Edificios'],
        summary: 'Obtener detalles completos de un edificio',
        description: 'Retorna todo lo necesario para la vista de detalles en una sola consulta: edificio, zona, otros edificios del bloque y todas las zonas',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'edificio', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Detalles del edificio', content: new OA\JsonContent(ref: '#/components/schemas/EdificioDetailResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'Edificio no encontrado'),
        ]
    )]
    public function detalleEdificio(Edificio $edificio): JsonResponse
    {
        // Obtener el edificio con sus clientes
        $edificio->load(['clientes']);

        // Obtener otros edificios del mismo bloque (misma dirección)
        $bloqueEdificios = Edificio::where('direccion_completa', $edificio->direccion_completa)
            ->where('id', '!=', $edificio->id)
            ->with(['clientes'])
            ->get();

        // Obtener la zona del edificio
        $zona = Zona::find($edificio->id_zona);

        // Obtener todas las zonas
        $todasLasZonas = Zona::all();

        // Asignar los datos al modelo para que el resource los encuentre
        $edificio->setRelation('zona', $zona);
        $edificio->setRelation('bloqueEdificios', $bloqueEdificios);
        $edificio->setRelation('todasLasZonas', $todasLasZonas);

        return response()->json(new EdificioDetailResource($edificio));
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
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $edificio = Edificio::create([
                'direccion_completa' => $data['direccion_completa'],
                'id_zona' => $data['id_zona'],
                'tipo' => $data['tipo'],
                'ubicacion' => $data['ubicacion'],
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear el edificio', 'message' => $e->getMessage()], 500);
        }

        return response()->json(
            new EdificioResource($edificio->fresh()->load(['zona', 'clientes'])),
            201
        );
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
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $updatable = array_filter(
                $request->only(['direccion_completa', 'id_zona', 'tipo', 'ubicacion']),
                fn($value) => $value !== null
            );

            if (!empty($updatable)) {
                $edificio->update($updatable);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar el edificio', 'message' => $e->getMessage()], 500);
        }

        return response()->json(new EdificioResource($edificio->fresh()->load(['zona', 'clientes'])));
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


    /**
     * Desadjuntar un cliente de un edificio
     */
    public function desadjuntarCliente(Edificio $edificio, int $clienteId): JsonResponse
    {
        try {
            // Desadjuntar el cliente del edificio
            $edificio->clientes()->detach($clienteId);

            return response()->json(
                new EdificioResource($edificio->load(['zona', 'clientes'])),
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Adjuntar múltiples clientes a un edificio en una sola operación
     */
    public function adjuntarVariosClientes(Edificio $edificio, Request $request): JsonResponse
    {
        $clientes = $request->input('clientes', []);

        if (!is_array($clientes) || empty($clientes)) {
            return response()->json(['error' => 'No se proporcionaron clientes'], 400);
        }

        try {
            DB::transaction(function () use ($edificio, $clientes) {
                $clientesParaAdjuntar = collect($clientes)->mapWithKeys(function ($cliente) {
                    $pivotData = array_filter([
                        'planta' => $cliente['planta'] ?? null,
                        'puerta' => $cliente['puerta'] ?? null,
                    ], fn($value) => $value !== null && $value !== '');

                    if (($cliente['mode'] ?? null) === 'crear') {
                        $nuevoCliente = Cliente::create([
                            'nombre' => $cliente['nombre'] ?? '',
                            'apellidos' => $cliente['apellidos'] ?? '',
                            'email' => $cliente['email'] ?? null,
                            'telefono' => $cliente['telefono'] ?? null,
                        ]);

                        return [$nuevoCliente->id => $pivotData];
                    }

                    if (($cliente['mode'] ?? null) === 'seleccionar' && !empty($cliente['clienteId'])) {
                        return [$cliente['clienteId'] => $pivotData];
                    }

                    return [];
                })->toArray();

                if (!empty($clientesParaAdjuntar)) {
                    $edificio->clientes()->syncWithoutDetaching($clientesParaAdjuntar);
                }
            });

            return response()->json(new EdificioResource($edificio->load(['zona', 'clientes'])), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al adjuntar clientes: ' . $e->getMessage()], 500);
        }
    }
}
