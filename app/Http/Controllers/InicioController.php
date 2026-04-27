<?php

namespace App\Http\Controllers;

use App\Http\Resources\inicioAdminResource;
use App\Http\Resources\inicioComercialResource;
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
     * Obtiene todos los datos consolidados necesarios para el inicio del Comercial, incluyendo:
     * - Edificios
     * - Clientes
     * - Visitas
     * - Estados de visita
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
        // El comentario de abajo es para evitar que el IDE marque como error la variable $user, 
        // que se usa en el código pero no se asigna explícitamente (se obtiene de Auth::user())
        /** @var User $user */
        $user = Auth::user();

        if (!$user || !$user->isComercial()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $edificios = Edificio::with(['clientes'])
            ->where('id_zona', $user->id_zona)
            ->get();

        $clienteIds = $edificios
            ->flatMap(fn($edificio) => $edificio->clientes)
            ->pluck('id')
            ->unique();

        $clientes = Cliente::whereIn('id', $clienteIds)->get();

        $visitas = Visita::with(['usuario', 'cliente', 'estado'])
            ->where('id_usuario', $user->id)
            ->get();

        return inicioComercialResource::make((object) [
            'edificios' => $edificios,
            'clientes' => $clientes,
            'visitas' => $visitas,
        ])
        ->response();
    }

    /**
     * Obtiene todos los datos consolidados necesarios para el inicio del Admin, incluyendo:
     * - Usuarios comerciales a su cargo
     * - Visitas
     * - Clientes
     * - Estados de visita
     * - Zonas
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
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $usuariosComerciales = User::where('rol', 'comercial')
            ->get(['id', 'nombre', 'apellidos', 'email', 'rol', 'id_responsable', 'id_zona']);

        $visitas = Visita::with(['estado'])
            ->get();

        $clientes = Cliente::select('id', 'nombre', 'apellidos', 'telefono', 'email')
            ->get();

        $edificios = Edificio::with(['clientes:id,nombre'])
            ->select('id', 'id_zona', 'direccion_completa', 'tipo')
            ->get();

        $estadosVisita = EstadoVisita::all();

        $zonas = Zona::select('id', 'nombre')->get();

        return inicioAdminResource::make((object) [
            'usuarios_comerciales' => $usuariosComerciales,
            'visitas' => $visitas,
            'clientes' => $clientes,
            'edificios' => $edificios,
            'estados_visita' => $estadosVisita,
            'zonas' => $zonas,
        ])
        ->response();
    }
}
