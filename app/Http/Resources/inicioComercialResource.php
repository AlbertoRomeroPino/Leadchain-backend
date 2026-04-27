<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class inicioComercialResource extends JsonResource
{
    /**
     * Disable the default "data" wrapping for this resource.
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'clientes' => $this->clientes->map(fn($c) => [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'apellidos' => $c->apellidos,
                'telefono' => $c->telefono,
                'email' => $c->email,
            ]),
            'visitas' => $this->visitas->map(fn($v) => [
                'id' => $v->id,
                'id_cliente' => $v->id_cliente,
                'fecha_hora' => $v->fecha_hora,
                'observaciones' => $v->observaciones,
                'estado' => $v->estado ? ['etiqueta' => $v->estado->etiqueta] : null,
            ]),
        ];
    }
}
