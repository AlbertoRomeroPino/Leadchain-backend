<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

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
        return $this->belongsToMany(Edificio::class, 'cliente_edificio', 'cliente_id', 'edificio_id')
            ->withPivot('planta', 'puerta');
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

    /**
     * Scope para filtrar clientes según el usuario autenticado. Para usarlo no hay
     * que poner el prefijo "scope" al llamarlo.
     */
    public function scopeFiltradoPorUsuario(Builder $query, User $user): Builder
    {
        if ($user->isComercial()) {
            return $query->whereHas('edificios', function ($q) use ($user) {
                if ($user->id_zona) {
                    $q->where('id_zona', $user->id_zona);
                }
            });
        }

        if ($user->isAdmin()) {
            $zoneIds = $user->subordinados()->pluck('id_zona')->filter()->toArray();
            if ($user->id_zona) {
                $zoneIds[] = $user->id_zona;
            }
            $zoneIds = array_unique($zoneIds);

            return !empty($zoneIds)
                ? $query->whereHas('edificios', fn($q) => $q->whereIn('id_zona', $zoneIds))
                : $query; 
        } else {
            return $query->whereHas('edificios');
        }
    }
}
