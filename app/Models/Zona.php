<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Zona extends Model
{
    use HasFactory;

    protected $table = 'zonas';

    protected $fillable = [
        'nombre_zona',
    ];

    protected $appends = [
        'esquina_noroeste',
        'esquina_noreste',
        'esquina_suroeste',
        'esquina_sureste',
    ];

    protected $hidden = [
        'esquina_noroeste_raw',
        'esquina_noreste_raw',
        'esquina_suroeste_raw',
        'esquina_sureste_raw',
    ];

    /**
     * Parsear geometry Point a array [lat, lng]
     */
    private function parsePoint(?string $column): ?array
    {
        if (!$this->id) return null;
        $result = DB::selectOne(
            "SELECT ST_Y({$column}) as lat, ST_X({$column}) as lng FROM zonas WHERE id = ?",
            [$this->id]
        );
        return $result ? ['lat' => (float) $result->lat, 'lng' => (float) $result->lng] : null;
    }

    public function getEsquinaNoroesteAttribute(): ?array
    {
        return $this->parsePoint('esquina_noroeste');
    }

    public function getEsquinaNoresteAttribute(): ?array
    {
        return $this->parsePoint('esquina_noreste');
    }

    public function getEsquinaSuroesteAttribute(): ?array
    {
        return $this->parsePoint('esquina_suroeste');
    }

    public function getEsquinaSuresteAttribute(): ?array
    {
        return $this->parsePoint('esquina_sureste');
    }

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
