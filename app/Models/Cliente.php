<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
     * Edificios del cliente (relación many-to-many)
     */
    public function edificios(): BelongsToMany
    {
        return $this->belongsToMany(Edificio::class, 'cliente_edificio', 'cliente_id', 'edificio_id');
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
