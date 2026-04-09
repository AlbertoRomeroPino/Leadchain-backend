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
     * Cliente propietario del edificio (relación antigua, mantener para compatibilidad)
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    /**
     * Clientes residentes en el edificio (relación many-to-many)
     */
    public function clientes(): BelongsToMany
    {
        return $this->belongsToMany(Cliente::class, 'cliente_edificio', 'edificio_id', 'cliente_id');
    }

    /**
     * Dirección completa con planta y puerta (del primer cliente)
     */
    public function getDireccionCompletaConPisoAttribute(): string
    {
        $direccion = $this->direccion_completa;
        
        // Obtener datos del primer cliente en la relación
        if ($this->relationLoaded('clientes') && $this->clientes->isNotEmpty()) {
            $primerCliente = $this->clientes->first();
            if ($primerCliente->pivot->planta) {
                $direccion .= ", {$primerCliente->pivot->planta}";
                if ($primerCliente->pivot->puerta) {
                    $direccion .= "º{$primerCliente->pivot->puerta}";
                }
            }
        }
        
        return $direccion;
    }
}
