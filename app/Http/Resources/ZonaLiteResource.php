<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ZonaLiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Lightweight version without edificios and usuarios relations
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre_zona' => $this->nombre_zona,
            'area' => $this->area,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
