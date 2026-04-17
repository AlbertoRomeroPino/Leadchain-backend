<?php

namespace App\Http\Controllers;

use App\Http\Requests\EdificioRequest;
use App\Http\Resources\EdificioResource;
use App\Http\Resources\EdificioDetailResource;
use App\Http\Resources\PanelEdificioResource;
use App\Http\Resources\ZonaResource;
use App\Models\Edificio;
use App\Models\Zona;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
        return response()->json(new EdificioResource($edificio->load(['zona', 'clientes'])));
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
    public function detalle(Edificio $edificio): JsonResponse
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

    /**
     * Obtener datos ligeros del edificio para panel de mapa
     * Retorna solo: id, dirección, tipo, ubicación, zona y clientes
     * Optimizado para MapaEdificioPanel
     */
    #[OA\Get(
        path: '/api/edificios/{edificio}/panel',
        tags: ['Edificios'],
        summary: 'Obtener datos ligeros de edificio para panel de mapa',
        description: 'Retorna solo la información necesaria para el panel del mapa: dirección, tipo, zona y clientes',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'edificio', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Panel del edificio', content: new OA\JsonContent(ref: '#/components/schemas/PanelEdificioResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'Edificio no encontrado'),
        ]
    )]
    public function panel(Edificio $edificio): JsonResponse
    {
        // Cargar solo lo necesario: zona y clientes
        $edificio->load(['zona', 'clientes']);

        return response()->json(new PanelEdificioResource($edificio));
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
                'INSERT INTO edificios (direccion_completa, id_zona, tipo, ubicacion, created_at, updated_at)
                 VALUES (?, ?, ?, ST_SetSRID(ST_MakePoint(?, ?), 4326), NOW(), NOW())
                 RETURNING id',
                [
                    $data['direccion_completa'],
                    $data['id_zona'],
                    $data['tipo'],
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

        return response()->json(new EdificioResource($edificio->fresh()->load(['zona', 'clientes'])), 201);
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
                'id_zona',
                'tipo',
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
     * Adjuntar un cliente a un edificio
     */
    public function attachCliente(Edificio $edificio, int $clienteId): JsonResponse
    {
        try {
            // Verificar que el cliente existe
            $cliente = \App\Models\Cliente::findOrFail($clienteId);
            
            // Obtener datos de piso y puerta del request
            $planta = request()->input('planta');
            $puerta = request()->input('puerta');
            
            // Adjuntar el cliente al edificio con datos pivot
            $pivotData = [];
            if ($planta) {
                $pivotData['planta'] = $planta;
            }
            if ($puerta) {
                $pivotData['puerta'] = $puerta;
            }
            
            $edificio->clientes()->syncWithoutDetaching([$clienteId => $pivotData]);

            return response()->json(
                new EdificioResource($edificio->load(['zona', 'clientes'])),
                200
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(
                ['error' => 'Cliente no encontrado'],
                404
            );
        } catch (\Exception $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Desadjuntar un cliente de un edificio
     */
    public function detachCliente(Edificio $edificio, int $clienteId): JsonResponse
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
    public function attachMultipleClientes(Edificio $edificio): JsonResponse
    {
        try {
            $clientes = request()->input('clientes', []);

            if (empty($clientes)) {
                return response()->json(
                    ['error' => 'No se proporcionaron clientes'],
                    400
                );
            }

            // Usar transacción para garantizar integridad de datos
            DB::beginTransaction();

            $syncData = [];

            foreach ($clientes as $cliente) {
                $mode = $cliente['mode'] ?? null;
                $planta = $cliente['planta'] ?? null;
                $puerta = $cliente['puerta'] ?? null;

                $pivotData = [];
                if ($planta) {
                    $pivotData['planta'] = $planta;
                }
                if ($puerta) {
                    $pivotData['puerta'] = $puerta;
                }

                if ($mode === 'crear') {
                    // Crear nuevo cliente
                    $nuevoCliente = \App\Models\Cliente::create([
                        'nombre' => $cliente['nombre'] ?? '',
                        'apellidos' => $cliente['apellidos'] ?? '',
                        'email' => $cliente['email'] ?? null,
                        'telefono' => $cliente['telefono'] ?? null,
                    ]);
                    $syncData[$nuevoCliente->id] = $pivotData;
                } elseif ($mode === 'seleccionar' && isset($cliente['clienteId'])) {
                    // Usar cliente existente
                    $syncData[$cliente['clienteId']] = $pivotData;
                }
            }

            // Adjuntar todos los clientes sin eliminar los existentes
            if (!empty($syncData)) {
                $edificio->clientes()->syncWithoutDetaching($syncData);
            }

            DB::commit();

            return response()->json(
                new EdificioResource($edificio->load(['zona', 'clientes'])),
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                ['error' => 'Error al adjuntar clientes: ' . $e->getMessage()],
                500
            );
        }
    }
}

