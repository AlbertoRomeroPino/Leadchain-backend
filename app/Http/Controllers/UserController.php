<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\ComercialesAMiCargoResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ZonaResource;
use App\Models\User;
use App\Models\Zona;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(
        path: '/api/users',
        tags: ['Usuarios'],
        summary: 'Obtener todos los usuarios',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Lista de usuarios', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/UserResource'))),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
        ]
    )]
    public function index(): JsonResponse
    {
        $users = User::all();
        return response()->json(UserResource::collection($users));
    }

    #[OA\Get(
        path: '/api/users/{user}',
        tags: ['Usuarios'],
        summary: 'Obtener un usuario por ID',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Usuario encontrado', content: new OA\JsonContent(ref: '#/components/schemas/UserResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 404, description: 'Usuario no encontrado'),
        ]
    )]
    public function show(User $user): JsonResponse
    {
        return response()->json(new UserResource($user));
    }

    #[OA\Post(
        path: '/api/users',
        tags: ['Usuarios'],
        summary: 'Crear un nuevo usuario',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 201, description: 'Usuario creado', content: new OA\JsonContent(ref: '#/components/schemas/UserResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(UserRequest $request): JsonResponse
    {
        $request['password'] = Hash::make($request['password']);

        $user = User::create($request->validated());

        return response()->json(new UserResource($user), 201);
    }

    #[OA\Put(
        path: '/api/users/{user}',
        tags: ['Usuarios'],
        summary: 'Actualizar un usuario completo',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'Usuario actualizado', content: new OA\JsonContent(ref: '#/components/schemas/UserResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    #[OA\Patch(
        path: '/api/users/{user}',
        tags: ['Usuarios'],
        summary: 'Actualizar parcialmente un usuario',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'Usuario actualizado', content: new OA\JsonContent(ref: '#/components/schemas/UserResource')),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        if (isset($request['password'])) {
            $request['password'] = Hash::make($request['password']);
        }

        $user->update($request->validated());

        return response()->json(new UserResource($user));
    }

    #[OA\Delete(
        path: '/api/users/{user}',
        tags: ['Usuarios'],
        summary: 'Eliminar un usuario',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Usuario eliminado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
        ]
    )]
    public function destroy(User $user): JsonResponse
    {
        DB::transaction(function () use ($user) {
            $user->visitas()->delete();
            $user->delete();
        });

        return response()->json(null, 204);
    }

    /**
     * Obtener comerciales del usuario actual con sus visitas, clientes y zonas
     * Consolida en una sola petición toda la información necesaria para ComercialesPage
     */
    #[OA\Get(
        path: '/api/users/comerciales-a-cargo',
        tags: ['Usuarios'],
        summary: 'Obtener comerciales a cargo del usuario actual con visitas consolidadas',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Comerciales con visitas, clientes y zonas consolidados',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'comerciales', type: 'array', items: new OA\Items(ref: '#/components/schemas/UserResource')),
                        new OA\Property(property: 'visitas', type: 'array', items: new OA\Items(ref: '#/components/schemas/VisitaResource')),
                        new OA\Property(property: 'clientes', type: 'array', items: new OA\Items(ref: '#/components/schemas/ClienteResource')),
                        new OA\Property(property: 'zonas', type: 'array', items: new OA\Items(ref: '#/components/schemas/ZonaResource')),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado (solo admin)'),
        ]
    )]
    public function comercialesACargo(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Solo admin puede ver sus comerciales
        /** @var User $user */
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Obtener comerciales del usuario actual (con sus subordinados)
        $comerciales = User::where('rol', 'comercial')
            ->where('id_responsable', $user->id)
            ->with(['visitas.cliente', 'visitas.estado'])
            ->get();

        // Obtener todas las zonas
        $zonas = Zona::get(['id', 'nombre']);

        return response()->json([
            'comerciales' => ComercialesAMiCargoResource::collection($comerciales),
            'zonas' => ZonaResource::collection($zonas),
        ]);
    }
}
