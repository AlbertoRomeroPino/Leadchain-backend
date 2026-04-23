<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZonaRequest;
use App\Http\Resources\MapaInicioResource;
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

            $zona = Zona::create(['nombre' => $request['nombre']]);
            $polygonWkt = $this->buildPolygonWkt($request['area']);

            DB::statement(
                "
            UPDATE zonas SET
                area = ST_GeomFromText(?, 4326)
            WHERE id = ?
        ",
                [
                    $polygonWkt,
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
        if (isset($request['nombre'])) {
            $zona->update(['nombre' => $request['nombre']]);
        }

        if (isset($request['area'])) {
            DB::statement(
                'UPDATE zonas SET area = ST_GeomFromText(?, 4326) WHERE id = ?',
                [$this->buildPolygonWkt($request['area']), $zona->id]
            );
        }

        return response()->json(new ZonaResource($zona->fresh()));
    }

    /**
     * Convierte un array de puntos [{lat, lng}, ...] en WKT POLYGON.
     */
    private function buildPolygonWkt(array $points): string
    {
        if (count($points) < 4) {
            throw new \InvalidArgumentException('El polígono debe contener al menos 4 puntos.');
        }

        $first = $points[0];
        $last = $points[count($points) - 1];

        if (!$this->isSamePoint($first, $last)) {
            $points[] = $first;
        }

        $coordinates = array_map(
            fn($point) => sprintf('%s %s', (float) $point['lng'], (float) $point['lat']),
            $points
        );

        return 'POLYGON((' . implode(', ', $coordinates) . '))';
    }

    private function isSamePoint(array $pointA, array $pointB): bool
    {
        return (float) $pointA['lat'] === (float) $pointB['lat']
            && (float) $pointA['lng'] === (float) $pointB['lng'];
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
                        new OA\Property(property: 'zonas', type: 'array', items: new OA\Items(ref: '#/components/schemas/ZonaResource')),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function datosMapaZonas(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Obtener zonas con edificios y clientes
        // Usar query raw para obtener coordenadas geospatiales en una sola consulta
        if ($user->rol === 'comercial' && $user->id_zona) {
            $zonas = Zona::where('id', $user->id_zona)
                ->with(['edificios.clientes'])
                ->get();
        } else {
            $zonas = Zona::with(['edificios.clientes'])->get();
        }

        // Obtener coordenadas para todos los edificios en una sola query
        $edificiosCoordenadas = DB::table('edificios')
            ->select('id', DB::raw('ST_Y(ubicacion) as lat'), DB::raw('ST_X(ubicacion) as lng'))
            ->get()
            ->keyBy('id');

        // Transformar directamente sin usar Resource
        $zonasTransformadas = $zonas->map(function ($zona) use ($edificiosCoordenadas) {
            return [
                'id' => $zona->id,
                'nombre' => $zona->nombre,
                'area' => $zona->area,
                'edificios' => $zona->edificios->map(function ($edificio) use ($edificiosCoordenadas) {
                    $coordenadas = $edificiosCoordenadas->get($edificio->id);
                    $ubicacion = $coordenadas ? [
                        'lat' => (float) $coordenadas->lat,
                        'lng' => (float) $coordenadas->lng,
                    ] : null;

                    return [
                        'id' => $edificio->id,
                        'nombre' => $edificio->nombre,
                        'direccion_completa' => $edificio->direccion_completa,
                        'tipo' => $edificio->tipo,
                        'ubicacion' => $ubicacion,
                        'id_zona' => $edificio->id_zona,
                        'clientes' => $edificio->clientes ? $edificio->clientes->map(fn($c) => [
                            'id' => $c->id,
                            'nombre' => $c->nombre,
                            'apellidos' => $c->apellidos,
                            'telefono' => $c->telefono,
                            'email' => $c->email,
                            'planta' => $c->pivot?->planta,
                            'puerta' => $c->pivot?->puerta,
                        ])->toArray() : [],
                    ];
                })->toArray(),
            ];
        });

        return response()->json([
            'zonas' => $zonasTransformadas,
        ]);
    }
}
