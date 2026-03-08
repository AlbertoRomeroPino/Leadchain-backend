<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoVisita extends Model
{
    use HasFactory;

    protected $table = 'estados_visita';

    protected $fillable = [
        'etiqueta',
        'color_hex',
    ];

    /**
     * Visitas con este estado
     */
    public function visitas(): HasMany
    {
        return $this->hasMany(Visita::class, 'id_estado');
    }
}
