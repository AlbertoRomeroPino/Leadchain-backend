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
            'nombre_zona' => $this->nombre_zona,
            'esquina_noroeste' => $this->esquina_noroeste,
            'esquina_noreste' => $this->esquina_noreste,
            'esquina_suroeste' => $this->esquina_suroeste,
            'esquina_sureste' => $this->esquina_sureste,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
