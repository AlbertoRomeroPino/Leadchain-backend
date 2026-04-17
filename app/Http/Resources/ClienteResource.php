<?php

namespace App\Http\Resources;

use App\Http\Resources\EdificioResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $edificios = [];
        if ($this->relationLoaded('edificios')) {
            $edificios = $this->edificios->map(function ($edificio) {
                $data = [
                    'id' => $edificio->id,
                    'direccion_completa' => $edificio->direccion_completa,
                    'ubicacion' => $edificio->ubicacion,
                    'id_zona' => $edificio->id_zona,
                    'tipo' => $edificio->tipo,
                    'created_at' => $edificio->created_at,
                    'updated_at' => $edificio->updated_at,
                ];
                
                // Incluir datos pivot (planta y puerta) si existen
                if ($edificio->pivot) {
                    $data['planta'] = $edificio->pivot->planta ?? null;
                    $data['puerta'] = $edificio->pivot->puerta ?? null;
                }
                
                return $data;
            })->toArray();
        }

        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'edificios' => $edificios,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
