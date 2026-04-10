<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitasPaginaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Retorna exactamente los datos necesarios para la página de Visitas
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'usuario' => $this->usuario ? [
                'id' => $this->usuario->id,
                'nombre' => $this->usuario->nombre,
                'apellidos' => $this->usuario->apellidos,
                'email' => $this->usuario->email,
            ] : null,
            'cliente' => $this->cliente ? [
                'id' => $this->cliente->id,
                'nombre' => $this->cliente->nombre,
                'apellidos' => $this->cliente->apellidos,
                'telefono' => $this->cliente->telefono,
                'email' => $this->cliente->email,
                'edificios' => $this->cliente->edificios ? $this->cliente->edificios->map(fn($e) => [
                    'id' => $e->id,
                    'direccion_completa' => $e->direccion_completa,
                ])->toArray() : [],
            ] : null,
            'estado' => $this->estado ? [
                'id' => $this->estado->id,
                'etiqueta' => $this->estado->etiqueta,
                'color_hex' => $this->estado->color_hex,
            ] : null,
            'fecha_hora' => $this->fecha_hora,
            'observaciones' => $this->observaciones,
        ];
    }
}
