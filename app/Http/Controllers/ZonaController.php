<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZonaRequest;
use App\Http\Resources\MapaZonasResource;
use App\Http\Resources\ZonaResource;
use App\Http\Resources\ZonaPageResource;
use App\Models\Zona;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
        path: '/api/zonas/pagina/datos',
        tags: ['Zonas'],
        summary: 'Obtener datos optimizados para la página de zonas',
        description: 'Retorna todas las zonas con sus edificios y clientes en una única consulta, optimizado para la visualización en la página',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Datos de zonas para la página', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/ZonaPageResource'))),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function datosPaginaZonas(): JsonResponse
    {
        $zonas = Zona::with(['edificios.clientes'])->get();
        return response()->json(ZonaPageResource::collection($zonas));
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

            $zona = Zona::create([
                'nombre' => $request['nombre'],
                'area' => $request['area'],
            ]);

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
            new OA\Response(response: 409, description: 'Conflicto: la zona contiene edificios y no puede ser modificada'),
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
            new OA\Response(response: 409, description: 'Conflicto: la zona contiene edificios y no puede ser modificada'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(ZonaRequest $request, Zona $zona): JsonResponse
    {
        // Obtener el área actual de la zona
        $areaActual = $zona->area;
        
        // Verificar si el área realmente cambió (no solo si se envía)
        $areaFueModificada = isset($request['area']) && $request['area'] !== $areaActual;
        
        // Si intenta modificar el área y la zona tiene edificios, rechazar
        if ($areaFueModificada && $zona->edificios()->exists()) {
            return response()->json([
                'error' => 'No se puede modificar el área de una zona que contiene edificios',
                'message' => 'Elimina o reasigna los edificios antes de modificar la zona'
            ], 409);
        }

        if (isset($request['nombre'])) {
            $zona->update(['nombre' => $request['nombre']]);
        }

        if (isset($request['area'])) {
            $zona->area = $request['area'];
            $zona->save();
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

    /**
     * Get consolidated data for map view
     * Returns: zonas with nested edificios and clientes in a single optimized query
     */
    #[OA\Get(
        path: '/api/zonas/mapa',
        tags: ['Zonas'],
        summary: 'Obtener zonas, edificios y clientes para vista de mapa (una sola consulta optimizada)',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Zonas con edificios y clientes anidados. Optimizado en una única query.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'zonas', type: 'array', items: new OA\Items(ref: '#/components/schemas/MapaZonasResource')),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function datosMapaZonas(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Obtener zonas con edificios (incluyendo coordenadas en selectRaw) y clientes
        // OPTIMIZACIÓN: selectRaw incluye coordenadas en la query principal, eliminando N queries extras
        $zonas = Zona::query();

        if ($user->isComercial() && $user->id_zona) {
            $zonas->where('id', $user->id_zona);
        }

        $zonas = $zonas->with([
            'edificios' => function ($query) {
                $query->select('id', 'direccion_completa', 'tipo', 'id_zona')
                      ->selectRaw('ST_Y("ubicacion") as lat, ST_X("ubicacion") as lng');
            },
            'edificios.clientes',
        ])->get();

        return response()->json([
            'zonas' => MapaZonasResource::collection($zonas),
        ]);
    }
}
