<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapaInicioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Retorna exactamente los datos necesarios para MapPage
     * Incluye zonas con edificios y clientes anidados
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre_zona' => $this->nombre_zona,
            'area' => $this->area,
            'edificios' => $this->whenLoaded('edificios', function () {
                return $this->edificios->map(function ($edificio) {
                    return [
                        'id' => $edificio->id,
                        'nombre' => $edificio->nombre,
                        'direccion_completa' => $edificio->direccion_completa,
                        'tipo' => $edificio->tipo,
                        'ubicacion' => $edificio->ubicacion,
                        'clientes' => $edificio->clientes ? [
                            'count' => $edificio->clientes->count(),
                        ] : [
                            'count' => 0,
                        ],
                    ];
                })->toArray();
            }, []),
        ];
    }
}
