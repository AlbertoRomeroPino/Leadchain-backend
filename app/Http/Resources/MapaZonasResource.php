<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapaZonasResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'area' => $this->area,
            'edificios' => $this->edificios->map(fn($edificio) => [
                'id' => $edificio->id,
                'direccion_completa' => $edificio->direccion_completa,
                'tipo' => $edificio->tipo,
                'id_zona' => $edificio->id_zona,
                'ubicacion' => $edificio->lat && $edificio->lng ? [
                    'lat' => (float) $edificio->lat,
                    'lng' => (float) $edificio->lng,
                ] : null,
                'clientes' => $edificio->clientes?->map(fn($cliente) => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'apellidos' => $cliente->apellidos,
                    'telefono' => $cliente->telefono,
                    'email' => $cliente->email,
                    'planta' => $cliente->pivot?->planta,
                    'puerta' => $cliente->pivot?->puerta,
                ])->toArray() ?? [],
            ])->toArray(),
        ];
    }
}
