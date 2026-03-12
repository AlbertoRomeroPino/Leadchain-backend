<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(UserResource::collection(User::with(['zona', 'responsable'])->get()));
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(new UserResource($user->load(['zona', 'responsable', 'subordinados', 'clientes'])));
    }

    public function store(UserRequest $request): JsonResponse
    {
        $request['password'] = Hash::make($request['password']);

        $user = User::create($request->validated());

        return response()->json(new UserResource($user), 201);
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        if (isset($request['password'])) {
            $request['password'] = Hash::make($request['password']);
        }

        $user->update($request->validated());

        return response()->json(new UserResource($user));
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
