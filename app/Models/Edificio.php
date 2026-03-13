<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Edificio extends Model
{
    use HasFactory;

    protected $table = 'edificios';

    protected $fillable = [
        'direccion_completa',
        'planta',
        'puerta',
        'id_zona',
        'tipo',
        'id_cliente',
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
     * Cliente propietario del edificio
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    /**
     * Dirección completa con planta y puerta
     */
    public function getDireccionCompletaConPisoAttribute(): string
    {
        $direccion = $this->direccion_completa;
        if ($this->planta) {
            $direccion .= ", {$this->planta}";
            if ($this->puerta) {
                $direccion .= "º{$this->puerta}";
            }
        }
        return $direccion;
    }
}
