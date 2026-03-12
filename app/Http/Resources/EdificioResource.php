<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EdificioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'direccion_completa' => $this->direccion_completa,
            'planta' => $this->planta,
            'puerta' => $this->puerta,
            'ubicacion' => $this->ubicacion,
            'id_zona' => $this->id_zona,
            'tipo' => $this->tipo,
            'id_cliente' => $this->id_cliente,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
