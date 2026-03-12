<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitaResource extends JsonResource
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
            'id_usuario' => $this->id_usuario,
            'id_cliente' => $this->id_cliente,
            'fecha_hora' => $this->fecha_hora,
            'hora_visita' => $this->hora_visita,
            'id_estado' => $this->id_estado,
            'observaciones' => $this->observaciones,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
