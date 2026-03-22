<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'apellidos',
        'telefono',
        'email',
    ];

    /**
     * Edificios del cliente
     */
    public function edificios(): HasMany
    {
        return $this->hasMany(Edificio::class, 'id_cliente');
    }

    /**
     * Visitas del cliente
     */
    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class, 'id_cliente');
    }

    /**
     * Nombre completo del cliente
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }
}
