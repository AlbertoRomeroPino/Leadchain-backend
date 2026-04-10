<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitaRequest;
use App\Http\Resources\VisitaResource;
use App\Http\Resources\VisitasPaginaResource;
use App\Models\Visita;
use App\Models\Cliente;
use App\Models\EstadoVisita;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();
        $query = Visita::with(['usuario', 'cliente.edificios', 'estado']);

        if ($user && $user->rol === 'comercial') {
            $query = $query->where('id_usuario', $user->id);
        } elseif ($user && $user->rol === 'admin') {
            $query = $query->where(function ($query) use ($user) {
                $query->where('id_usuario', $user->id)
                    ->orWhereHas('usuario', function ($query) use ($user) {
                        $query->where('id_responsable', $user->id);
                    });
            });
        } else {
            $query = $query->where('id_usuario', $user?->id ?? 0);
        }

        return response()->json(VisitaResource::collection($query->get()));
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

    /**
     * Get consolidated data for visitas page
     * Returns: visitas, clientes, and estados in a single optimized query
     * This reduces 3 API calls to 1
     */
    #[OA\Get(
        path: '/api/visitas/pagina/datos-consolidados',
        tags: ['Visitas'],
        summary: 'Obtener datos consolidados para página de visitas (una sola consulta)',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Visitas, clientes y estados consolidados',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'visitas', type: 'array', items: new OA\Items(ref: '#/components/schemas/VisitasPaginaResource')),
                        new OA\Property(property: 'clientes', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'estados', type: 'array', items: new OA\Items(type: 'object')),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function paraVisitasPage(): JsonResponse
    {
        $user = Auth::user();

        // Obtener visitas con relaciones eager loaded
        $visitasQuery = Visita::with(['usuario', 'cliente.edificios', 'estado']);

        if ($user && $user->rol === 'comercial') {
            $visitasQuery = $visitasQuery->where('id_usuario', $user->id);
        } elseif ($user && $user->rol === 'admin') {
            $visitasQuery = $visitasQuery->where(function ($q) use ($user) {
                $q->where('id_usuario', $user->id)
                    ->orWhereHas('usuario', function ($q) use ($user) {
                        $q->where('id_responsable', $user->id);
                    });
            });
        }

        $visitas = $visitasQuery->get();

        // Obtener TODOS los clientes (necesarios para el formulario)
        $clientes = Cliente::with('edificios')->get();

        // Obtener TODOS los estados
        $estados = EstadoVisita::all();

        return response()->json([
            'visitas' => VisitasPaginaResource::collection($visitas),
            'clientes' => $clientes->map(fn($c) => [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'apellidos' => $c->apellidos,
                'telefono' => $c->telefono,
                'email' => $c->email,
                'edificios' => $c->edificios ? $c->edificios->map(fn($e) => [
                    'id' => $e->id,
                    'direccion_completa' => $e->direccion_completa,
                ])->toArray() : [],
            ])->toArray(),
            'estados' => $estados->map(fn($e) => [
                'id' => $e->id,
                'etiqueta' => $e->etiqueta,
                'color_hex' => $e->color_hex,
            ])->toArray(),
        ]);
    }
}
