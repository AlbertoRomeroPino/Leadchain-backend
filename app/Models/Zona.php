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
        // Esquina Noroeste
        'lat_noroeste',
        'lng_noroeste',
        // Esquina Noreste
        'lat_noreste',
        'lng_noreste',
        // Esquina Suroeste
        'lat_suroeste',
        'lng_suroeste',
        // Esquina Sureste
        'lat_sureste',
        'lng_sureste',
    ];

    protected $casts = [
        'lat_noroeste' => 'float',
        'lng_noroeste' => 'float',
        'lat_noreste' => 'float',
        'lng_noreste' => 'float',
        'lat_suroeste' => 'float',
        'lng_suroeste' => 'float',
        'lat_sureste' => 'float',
        'lng_sureste' => 'float',
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
