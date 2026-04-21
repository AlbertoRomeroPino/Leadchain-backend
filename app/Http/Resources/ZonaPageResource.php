<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ZonaPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Optimized for ZonaPage - includes zona data plus associated edificios with clientes
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'area' => $this->area,
            'edificios' => EdificioResource::collection($this->whenLoaded('edificios')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
