<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminInicioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Retorna exactamente los datos necesarios para InicioAdmin
     */
    public function toArray(Request $request): array
    {
        return [
            'usuarios_comerciales' => $this->when(
                isset($this->usuarios_comerciales),
                function () {
                    return $this->usuarios_comerciales->map(function ($usuario) {
                        return [
                            'id' => $usuario->id,
                            'nombre' => $usuario->nombre,
                            'apellidos' => $usuario->apellidos,
                            'email' => $usuario->email,
                            'rol' => $usuario->rol,
                            'id_responsable' => $usuario->id_responsable,
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
                            'id_usuario' => $visita->id_usuario,
                            'id_cliente' => $visita->id_cliente,
                            'id_estado' => $visita->id_estado,
                            'fecha_hora' => $visita->fecha_hora,
                            'observaciones' => $visita->observaciones,
                            'estado' => $visita->estado ? [
                                'id' => $visita->estado->id,
                                'etiqueta' => $visita->estado->etiqueta,
                            ] : null,
                        ];
                    })->toArray();
                }
            ),
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
            'edificios' => $this->when(
                isset($this->edificios),
                function () {
                    return $this->edificios->map(function ($edificio) {
                        return [
                            'id' => $edificio->id,
                            'direccion_completa' => $edificio->direccion_completa,
                            'tipo' => $edificio->tipo,
                        ];
                    })->toArray();
                }
            ),
        ];
    }
}
