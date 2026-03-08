<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visita extends Model
{
    use HasFactory;

    protected $table = 'visitas';

    protected $fillable = [
        'id_usuario',
        'id_cliente',
        'fecha_hora',
        'hora_visita',
        'id_estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'hora_visita' => 'datetime:H:i:s',
    ];

    /**
     * Usuario que realiza la visita
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Cliente visitado
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    /**
     * Estado de la visita
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoVisita::class, 'id_estado');
    }
}
