<?php

namespace App\Http\Controllers;

use App\Http\Requests\EdificioRequest;
use App\Http\Resources\EdificioResource;
use App\Models\Edificio;
use Illuminate\Http\JsonResponse;

class EdificioController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(EdificioResource::collection(Edificio::with(['zona', 'cliente'])->get()));
    }

    public function show(Edificio $edificio): JsonResponse
    {
        return response()->json(new EdificioResource($edificio->load(['zona', 'cliente'])));
    }

    public function store(EdificioRequest $request): JsonResponse
    {

        $edificio = Edificio::create($request->validated());

        return response()->json(new EdificioResource($edificio), 201);
    }

    public function update(EdificioRequest $request, Edificio $edificio): JsonResponse
    {

        $edificio->update($request->validated());

        return response()->json(new EdificioResource($edificio));
    }

    public function destroy(Edificio $edificio): JsonResponse
    {
        $edificio->delete();

        return response()->json(null, 204);
    }
}
