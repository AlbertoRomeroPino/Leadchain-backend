<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EdificioDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Optimized for EdificioInfo component - includes all needed info in one request
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'direccion_completa' => $this->direccion_completa,
            'ubicacion' => $this->ubicacion,
            'id_zona' => $this->id_zona,
            'tipo' => $this->tipo,
            'id_cliente' => $this->id_cliente,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Relación con clientes (many-to-many con pivot)
            'clientes' => $this->whenLoaded('clientes', function () {
                return $this->clientes->map(function ($cliente) {
                    return [
                        'id' => $cliente->id,
                        'nombre' => $cliente->nombre,
                        'apellidos' => $cliente->apellidos,
                        'telefono' => $cliente->telefono,
                        'email' => $cliente->email,
                        'planta' => $cliente->pivot?->planta,
                        'puerta' => $cliente->pivot?->puerta,
                    ];
                });
            }),
            // Zona del edificio
            'zona' => $this->whenLoaded('zona', new ZonaResource($this->zona)),
            // Otros edificios del mismo bloque (misma dirección)
            'bloqueEdificios' => $this->whenLoaded('bloqueEdificios', EdificioDetailResource::collection($this->bloqueEdificios)),
            // Todas las zonas disponibles
            'todasLasZonas' => $this->whenLoaded('todasLasZonas', ZonaResource::collection($this->todasLasZonas)),
        ];
    }
}
