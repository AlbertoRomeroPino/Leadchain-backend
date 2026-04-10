<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComercialInicioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Retorna exactamente los datos necesarios para InicioComercial
     */
    public function toArray(Request $request): array
    {
        return [
            'clientes' => $this->when(
                isset($this->clientes),
                function () {
                    return $this->clientes->map(function ($cliente) {
                        return [
                            'id' => $cliente->id,
                            'nombre' => $cliente->nombre,
                            'apellidos' => $cliente->apellidos,
                            'telefono' => $cliente->telefono,
                            'email' => $cliente->email,
                        ];
                    })->toArray();
                }
            ),
            'visitas' => $this->when(
                isset($this->visitas),
                function () {
                    return $this->visitas->map(function ($visita) {
                        return [
                            'id' => $visita->id,
                            'id_cliente' => $visita->id_cliente,
                            'fecha_hora' => $visita->fecha_hora,
                            'observaciones' => $visita->observaciones,
                            'estado' => $visita->estado ? [
                                'etiqueta' => $visita->estado->etiqueta,
                            ] : null,
                        ];
                    })->toArray();
                }
            ),
        ];
    }
}
