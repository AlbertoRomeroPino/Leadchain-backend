<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Edificio extends Model
{
    use HasFactory;

    protected $table = 'edificios';

    protected $fillable = [
        'direccion_completa',
        'id_zona',
        'tipo',
        'ubicacion',
    ];

    /**
     * Parsear geometry Point a array [lat, lng]
     */
    public function getUbicacionAttribute(): ?array
    {
        if (!$this->id) {
            return null;
        }

        $result = DB::selectOne(
            'SELECT ST_Y(ubicacion) as lat, ST_X(ubicacion) as lng FROM edificios WHERE id = ?',
            [$this->id]
        );

        return $result ? ['lat' => (float) $result->lat, 'lng' => (float) $result->lng] : null;
    }

    /**
     * Zona donde está el edificio
     */
    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zona::class, 'id_zona');
    }

    /**
     * Clientes residentes en el edificio (relación many-to-many)
     */
    public function clientes(): BelongsToMany
    {
        return $this->belongsToMany(Cliente::class, 'cliente_edificio', 'edificio_id', 'cliente_id')
            ->withPivot('planta', 'puerta');
    }

    public function setUbicacionAttribute($value): void
    {
        if (is_array($value) && isset($value['lng'], $value['lat'])) {
            $this->attributes['ubicacion'] = DB::raw(
                "ST_SetSRID(ST_MakePoint({$value['lng']}, {$value['lat']}), 4326)"
            );
        }
    }
}
