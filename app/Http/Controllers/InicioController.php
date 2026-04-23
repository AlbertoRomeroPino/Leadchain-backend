<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminInicioResource;
use App\Http\Resources\ComercialInicioResource;
use App\Http\Resources\EstadoVisitaResource;
use App\Models\Cliente;
use App\Models\Edificio;
use App\Models\EstadoVisita;
use App\Models\User;
use App\Models\Visita;
use App\Models\Zona;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class InicioController extends Controller
{
    /**
     * Get consolidated data for commercial dashboard
     * Returns: edificios, clientes, visitas y estados de visita
     */
    #[OA\Get(
        path: '/api/inicio/comercial',
        tags: ['Inicio'],
        summary: 'Obtener datos consolidados para inicio comercial',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Datos consolidados del inicio comercial',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'edificios', type: 'array', items: new OA\Items(ref: '#/components/schemas/EdificioResource')),
                        new OA\Property(property: 'clientes', type: 'array', items: new OA\Items(ref: '#/components/schemas/ClienteResource')),
                        new OA\Property(property: 'visitas', type: 'array', items: new OA\Items(ref: '#/components/schemas/VisitaResource')),
                        new OA\Property(property: 'estados_visita', type: 'array', items: new OA\Items(ref: '#/components/schemas/EstadoVisitaResource')),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado'),
        ]
    )]
    public function datosInicioComercial(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->rol !== 'comercial') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Obtener edificios de la zona del comercial CON clientes anidados
        $edificios = Edificio::where('id_zona', $user->id_zona)
            ->with(['clientes'])
            ->get();

        // Obtener IDs únicos de clientes de esos edificios
        $clienteIds = $edificios
            ->flatMap(fn($ed) => $ed->clientes ?? [])
            ->unique('id')
            ->pluck('id')
            ->toArray();

        // Obtener clientes
        $clientes = Cliente::whereIn('id', $clienteIds)
            ->get();

        // Obtener visitas del comercial actual
        $visitas = Visita::where('id_usuario', $user->id)
            ->with(['usuario', 'cliente', 'estado'])
            ->get();

        // Crear un objeto con toda la información
        $data = (object)[
            'clientes' => $clientes,
            'visitas' => $visitas,
        ];

        return response()->json([
            'clientes' => $clientes->map(fn($c) => [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'apellidos' => $c->apellidos,
                'telefono' => $c->telefono,
                'email' => $c->email,
            ]),
            'visitas' => $visitas->map(fn($v) => [
                'id' => $v->id,
                'id_cliente' => $v->id_cliente,
                'fecha_hora' => $v->fecha_hora,
                'observaciones' => $v->observaciones,
                'estado' => $v->estado ? ['etiqueta' => $v->estado->etiqueta] : null,
            ]),
        ]);
    }

    /**
     * Get consolidated data for admin dashboard
     * Returns: usuarios comerciales, visitas, clientes y tipos de edificios
     */
    #[OA\Get(
        path: '/api/inicio/admin',
        tags: ['Inicio'],
        summary: 'Obtener datos consolidados para inicio admin',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Datos consolidados del inicio admin',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'usuarios_comerciales', type: 'array', items: new OA\Items(ref: '#/components/schemas/UserResource')),
                        new OA\Property(property: 'visitas', type: 'array', items: new OA\Items(ref: '#/components/schemas/VisitaResource')),
                        new OA\Property(property: 'clientes', type: 'array', items: new OA\Items(ref: '#/components/schemas/ClienteResource')),
                        new OA\Property(property: 'edificios', type: 'array', items: new OA\Items(ref: '#/components/schemas/EdificioResource')),
                        new OA\Property(property: 'estados_visita', type: 'array', items: new OA\Items(ref: '#/components/schemas/EstadoVisitaResource')),
                        new OA\Property(property: 'zonas', type: 'array', items: new OA\Items(ref: '#/components/schemas/ZonaResource')),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado'),
        ]
    )]
    public function datosInicioAdmin(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->rol !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Obtener todos los usuarios comerciales
        $usuariosComerciales = User::where('rol', 'comercial')
            ->get();

        // Obtener todas las visitas con relaciones
        $visitas = Visita::with(['usuario', 'cliente', 'estado'])
            ->get();

        // Obtener todos los clientes
        $clientes = Cliente::get();

        // Obtener todos los edificios CON clientes anidados
        $edificios = Edificio::with(['clientes'])->get();

        // Obtener todos los estados de visita
        $estadosVisita = EstadoVisita::all();

        // Obtener todas las zonas
        $zonas = Zona::all();

        // Crear un objeto con toda la información
        $data = (object)[
            'usuarios_comerciales' => $usuariosComerciales,
            'visitas' => $visitas,
            'clientes' => $clientes,
            'edificios' => $edificios,
            'estados_visita' => $estadosVisita,
            'zonas' => $zonas,
        ];

        return response()->json([
            'usuarios_comerciales' => $usuariosComerciales->map(fn($u) => ['id' => $u->id, 'nombre' => $u->nombre, 'apellidos' => $u->apellidos, 'email' => $u->email, 'rol' => $u->rol, 'id_responsable' => $u->id_responsable, 'id_zona' => $u->id_zona]),
            'visitas' => $visitas->map(fn($v) => [
                'id' => $v->id,
                'id_usuario' => $v->id_usuario,
                'id_cliente' => $v->id_cliente,
                'fecha_hora' => $v->fecha_hora,
                'observaciones' => $v->observaciones,
                'estado' => $v->estado ? ['id' => $v->estado->id, 'etiqueta' => $v->estado->etiqueta] : null,
            ]),
            'clientes' => $clientes->map(fn($c) => [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'apellidos' => $c->apellidos,
                'telefono' => $c->telefono,
                'email' => $c->email,
            ]),
            'edificios' => $edificios->map(fn($e) => [
                'id' => $e->id,
                'id_zona' => $e->id_zona,
                'direccion_completa' => $e->direccion_completa,
                'tipo' => $e->tipo,
                'clientes' => $e->clientes ? $e->clientes->map(fn($c) => ['id' => $c->id, 'nombre' => $c->nombre]) : [],
            ]),
            'estados_visita' => $estadosVisita,
            'zonas' => $zonas->map(fn($z) => ['id' => $z->id, 'nombre' => $z->nombre]),
        ]);
    }
}
