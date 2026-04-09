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
        $data = [
            'id' => $this->id,
            'direccion_completa' => $this->direccion_completa,
            'ubicacion' => $this->ubicacion,
            'id_zona' => $this->id_zona,
            'tipo' => $this->tipo,
            'id_cliente' => $this->id_cliente,
            'cliente' => $this->whenLoaded('cliente'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Obtener planta y puerta del primer cliente si existen
        if ($this->relationLoaded('clientes') && $this->clientes->isNotEmpty()) {
            $primerCliente = $this->clientes->first();
            $data['planta'] = $primerCliente->pivot ? $primerCliente->pivot->planta : null;
            $data['puerta'] = $primerCliente->pivot ? $primerCliente->pivot->puerta : null;
        } else {
            $data['planta'] = null;
            $data['puerta'] = null;
        }

        // Si se cargaron los clientes, incluir planta y puerta desde el pivot
        if ($this->relationLoaded('clientes')) {
            $data['clientes'] = $this->clientes->map(function ($cliente) {
                return [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'apellidos' => $cliente->apellidos,
                    'telefono' => $cliente->telefono,
                    'email' => $cliente->email,
                    'planta' => $cliente->pivot ? $cliente->pivot->planta : null,
                    'puerta' => $cliente->pivot ? $cliente->pivot->puerta : null,
                ];
            })->toArray();
        }

        return $data;
    }
}
