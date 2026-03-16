<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ClienteController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/clientes",
     * summary="Listar clientes",
     * tags={"Clientes"},
     * @OA\Response(response=200, description="OK"),
     * security={{"bearerAuth":{}}}
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json(ClienteResource::collection(Cliente::with('usuarioAsignado')->get()));
    }

    public function show(Cliente $cliente): JsonResponse
    {
        return response()->json(new ClienteResource($cliente->load(['usuarioAsignado', 'edificios', 'visitas'])));
    }

    public function store(ClienteRequest $request): JsonResponse
    {
        $cliente = Cliente::create($request->validated());

        return response()->json(new ClienteResource($cliente), 201);
    }

    public function update(ClienteRequest $request, Cliente $cliente): JsonResponse
    {
        $cliente->update($request->validated());

        return response()->json(new ClienteResource($cliente));
    }

    public function destroy(Cliente $cliente): JsonResponse
    {
        $cliente->delete();

        return response()->json(null, 204);
    }
}
