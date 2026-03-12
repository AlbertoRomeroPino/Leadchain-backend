<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'apellidos' => $this->apellidos,
            'email' => $this->email,
            'rol' => $this->rol,
            'id_responsable' => $this->id_responsable,
            'id_zona' => $this->id_zona,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
