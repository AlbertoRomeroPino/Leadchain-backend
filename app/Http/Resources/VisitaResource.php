<?php

namespace App\Http\Resources;

use App\Http\Resources\ClienteResource;
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
            'id_estado' => $this->id_estado,
            'observaciones' => $this->observaciones,
            'usuario' => $this->whenLoaded('usuario', function () {
                return [
                    'id' => $this->usuario?->id,
                    'nombre' => $this->usuario?->nombre,
                    'apellidos' => $this->usuario?->apellidos,
                ];
            }),
            'cliente' => $this->whenLoaded('cliente', function () {
                return ClienteResource::make($this->cliente);
            }),
            'estado' => $this->whenLoaded('estado', function () {
                return [
                    'id' => $this->estado?->id,
                    'etiqueta' => $this->estado?->etiqueta,
                    'color_hex' => $this->estado?->color_hex,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
