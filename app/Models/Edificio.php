<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Edificio extends Model
{
    use HasFactory;

    protected $table = 'edificios';

    protected $fillable = [
        'direccion_completa',
        'planta',
        'puerta',
        'ubicacion',
        'id_zona',
        'tipo',
        'id_cliente',
    ];

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
