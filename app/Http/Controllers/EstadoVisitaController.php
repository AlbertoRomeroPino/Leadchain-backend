<?php

namespace App\Http\Controllers;

use App\Http\Requests\EstadoVisitaRequest;
use App\Http\Resources\EstadoVisitaResource;
use App\Models\EstadoVisita;
use Illuminate\Http\JsonResponse;

class EstadoVisitaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(EstadoVisitaResource::collection(EstadoVisita::all()));
    }

    public function show(EstadoVisita $estadoVisita): JsonResponse
    {
        return response()->json(new EstadoVisitaResource($estadoVisita));
    }

    public function store(EstadoVisitaRequest $request): JsonResponse
    {


        $estadoVisita = EstadoVisita::create($request->validated());

        return response()->json(new EstadoVisitaResource($estadoVisita), 201);
    }

    public function update(EstadoVisitaRequest $request, EstadoVisita $estadoVisita): JsonResponse
    {


        $estadoVisita->update($request->validated());

        return response()->json(new EstadoVisitaResource($estadoVisita));
    }

    public function destroy(EstadoVisita $estadoVisita): JsonResponse
    {
        $estadoVisita->delete();

        return response()->json(null, 204);
    }
}
