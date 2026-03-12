<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstadoVisitaResource extends JsonResource
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
            'etiqueta' => $this->etiqueta,
            'color_hex' => $this->color_hex,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
