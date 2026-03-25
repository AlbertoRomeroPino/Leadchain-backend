<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteDetalleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $edificio = $this->edificios->first();

        $ultimaVisita = $this->visitas
            ->sortByDesc('fecha_hora')
            ->first();

        return [
            'cliente' => [
                'id' => $this->id,
                'nombre' => $this->nombre,
                'apellidos' => $this->apellidos,
                'telefono' => $this->telefono,
                'email' => $this->email,
            ],
            'edificio' => $edificio ? [
                'id' => $edificio->id,
                'direccion_completa' => $edificio->direccion_completa,
                'planta' => $edificio->planta,
                'puerta' => $edificio->puerta,
                'ubicacion' => $edificio->ubicacion,
                'tipo' => $edificio->tipo,
                'id_zona' => $edificio->id_zona,
                'zona' => $edificio->zona ? [
                    'id' => $edificio->zona->id,
                    'nombre_zona' => $edificio->zona->nombre_zona,
                ] : null,
            ] : null,
            'visitas' => [
                'total' => $this->visitas->count(),
                'ultima' => $ultimaVisita ? [
                    'id' => $ultimaVisita->id,
                    'id_usuario' => $ultimaVisita->id_usuario,
                    'id_cliente' => $ultimaVisita->id_cliente,
                    'id_estado' => $ultimaVisita->id_estado,
                    'fecha_hora' => $ultimaVisita->fecha_hora,
                    'observaciones' => $ultimaVisita->observaciones,
                    'estado' => $ultimaVisita->estado ? [
                        'id' => $ultimaVisita->estado->id,
                        'etiqueta' => $ultimaVisita->estado->etiqueta,
                        'color_hex' => $ultimaVisita->estado->color_hex,
                    ] : null,
                    'usuario' => $ultimaVisita->usuario ? [
                        'id' => $ultimaVisita->usuario->id,
                        'nombre' => $ultimaVisita->usuario->nombre,
                        'apellidos' => $ultimaVisita->usuario->apellidos,
                    ] : null,
                ] : null,
            ],
        ];
    }
}