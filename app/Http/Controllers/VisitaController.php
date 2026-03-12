<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitaRequest;
use App\Http\Resources\VisitaResource;
use App\Models\Visita;
use Illuminate\Http\JsonResponse;

class VisitaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(VisitaResource::collection(Visita::with(['usuario', 'cliente', 'estado'])->get()));
    }

    public function show(Visita $visita): JsonResponse
    {
        return response()->json(new VisitaResource($visita->load(['usuario', 'cliente', 'estado'])));
    }

    public function store(VisitaRequest $request): JsonResponse
    {


        $visita = Visita::create($request->validated());

        return response()->json(new VisitaResource($visita), 201);
    }

    public function update(VisitaRequest $request, Visita $visita): JsonResponse
    {
        $visita->update($request->validated());

        return response()->json(new VisitaResource($visita));
    }

    public function destroy(Visita $visita): JsonResponse
    {
        $visita->delete();

        return response()->json(null, 204);
    }
}
