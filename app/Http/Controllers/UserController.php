<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(
        path: '/api/users',
        tags: ['Usuarios'],
        summary: 'Listar todos los usuarios',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Listado de usuarios', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/UserResource'))),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'No autorizado por rol'),
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(UserResource::collection(User::with(['zona', 'responsable'])->get()));
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
        return response()->json(new UserResource($user->load(['zona', 'responsable', 'subordinados', 'clientes'])));
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
        $user->delete();

        return response()->json(null, 204);
    }
}
