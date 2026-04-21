<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ZonaResource extends JsonResource
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
            'nombre' => $this->nombre,
            'area' => $this->area,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'usuarios' => $this->whenLoaded('usuarios', function () {
                return $this->usuarios;
            }),
            'edificios' => EdificioResource::collection($this->whenLoaded('edificios', $this->edificios)),
        ];
    }
}
