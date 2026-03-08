<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zona extends Model
{
    use HasFactory;

    protected $table = 'zonas';

    protected $fillable = [
        'nombre_zona',
        'poligono_coordenadas',
    ];

    /**
     * Usuarios asignados a esta zona
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'id_zona');
    }

    /**
     * Edificios en esta zona
     */
    public function edificios(): HasMany
    {
        return $this->hasMany(Edificio::class, 'id_zona');
    }
}
