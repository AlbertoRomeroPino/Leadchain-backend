<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResponseResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'message' => $this->message ?? 'Login exitoso',
            'access_token' => $this->access_token,
            'token_type' => 'bearer',
            'expires_in' => $this->expires_in ?? 3600,
            'user' => [
                'id' => $this->user->id,
                'nombre' => $this->user->nombre,
                'apellidos' => $this->user->apellidos,
                'email' => $this->user->email,
                'rol' => $this->user->rol,
            ],
            'inicio' => $this->inicio,
        ];
    }
}
