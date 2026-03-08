<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'apellidos',
        'email',
        'password',
        'rol',
        'id_responsable',
        'id_zona',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Responsable del usuario (supervisor)
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_responsable');
    }

    /**
     * Usuarios bajo su responsabilidad
     */
    public function subordinados(): HasMany
    {
        return $this->hasMany(User::class, 'id_responsable');
    }

    /**
     * Zona asignada al usuario
     */
    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zona::class, 'id_zona');
    }

    /**
     * Clientes asignados al usuario
     */
    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'id_usuario_asignado');
    }

    /**
     * Visitas del usuario
     */
    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class, 'id_usuario');
    }
}
