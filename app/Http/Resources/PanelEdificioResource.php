<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PanelEdificioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Lightweight version for MapaEdificioPanel - only zona and clientes
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'direccion_completa' => $this->direccion_completa,
            'tipo' => $this->tipo,
            'ubicacion' => $this->ubicacion,
            'id_zona' => $this->id_zona,
            // Zona del edificio
            'zona' => $this->whenLoaded('zona', new ZonaLiteResource($this->zona)),
            // Clientes con planta y puerta del pivot
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
        ];
    }
}
