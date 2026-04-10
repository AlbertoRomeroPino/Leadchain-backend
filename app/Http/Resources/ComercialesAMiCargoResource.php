<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComercialesAMiCargoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Retorna exactamente los datos necesarios para ComercialesPage
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'email' => $this->email,
            'id_zona' => $this->id_zona,
            'visitas' => $this->whenLoaded('visitas', function () {
                return $this->visitas->map(function ($visita) {
                    return [
                        'id' => $visita->id,
                        'fecha_hora' => $visita->fecha_hora,
                        'id_cliente' => $visita->id_cliente,
                        'id_estado' => $visita->id_estado,
                        'observaciones' => $visita->observaciones,
                        'cliente' => $visita->cliente ? [
                            'nombre' => $visita->cliente->nombre,
                            'apellidos' => $visita->cliente->apellidos,
                        ] : null,
                        'estado' => $visita->estado ? [
                            'etiqueta' => $visita->estado->etiqueta,
                        ] : null,
                    ];
                })->toArray();
            }, []),
        ];
    }
}
