<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Cliente;
use App\Models\Edificio;
use App\Models\EstadoVisita;
use App\Models\User;
use App\Models\Visita;
use App\Models\Zona;

class inicioAdminResource extends JsonResource
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
            'usuarios_comerciales' => $this->usuarios_comerciales->map(fn($u) => [
                'id' => $u->id,
                'nombre' => $u->nombre,
                'apellidos' => $u->apellidos,
                'email' => $u->email,
                'rol' => $u->rol,
                'id_responsable' => $u->id_responsable,
                'id_zona' => $u->id_zona,
            ]),
            'visitas' => $this->visitas->map(fn($v) => [
                'id' => $v->id,
                'id_usuario' => $v->id_usuario,
                'id_cliente' => $v->id_cliente,
                'fecha_hora' => $v->fecha_hora,
                'observaciones' => $v->observaciones,
                'estado' => $v->estado ? ['id' => $v->estado->id, 'etiqueta' => $v->estado->etiqueta] : null,
            ]),
            'clientes' => $this->clientes->map(fn($c) => [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'apellidos' => $c->apellidos,
                'telefono' => $c->telefono,
                'email' => $c->email,
            ]),
            'edificios' => $this->edificios->map(fn($e) => [
                'id' => $e->id,
                'id_zona' => $e->id_zona,
                'direccion_completa' => $e->direccion_completa,
                'tipo' => $e->tipo,
                'clientes' => $e->clientes->map(fn($c) => ['id' => $c->id, 'nombre' => $c->nombre]),
            ]),
            'estados_visita' => $this->estados_visita,
            'zonas' => $this->zonas->map(fn($z) => ['id' => $z->id, 'nombre' => $z->nombre]),
        ];
    }
}
