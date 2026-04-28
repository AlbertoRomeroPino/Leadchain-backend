<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class datosPaginaVisitasResources extends JsonResource
{
    /**
     * Disable the default data wrapper for this resource.
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
            'visitas' => VisitasPaginaResource::collection($this->visitas),
            'clientes' => $this->clientes->map(fn($cliente) => [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'apellidos' => $cliente->apellidos,
                'telefono' => $cliente->telefono,
                'email' => $cliente->email,
                'edificios' => collect($cliente->edificios ?? [])->map(fn($edificio) => [
                    'id' => $edificio->id,
                    'direccion_completa' => $edificio->direccion_completa,
                ]),
            ]),
            'estados' => $this->estados->map(fn($estado) => [
                'id' => $estado->id,
                'etiqueta' => $estado->etiqueta,
                'color_hex' => $estado->color_hex,
            ]),
        ];
    }
}
