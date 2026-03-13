<?php

namespace tests\Fixture;

class VisitasData
{
    const VISITA_POST = [
        "id_usuario" => 2,
        "id_cliente" => 1,
        "fecha_hora" => "2026-03-15 10:30:00",
        "id_estado" => 1,
        "observaciones" => "Primera visita al cliente"
    ];

    const VISITA_PUT = [
        "id_usuario" => 2,
        "id_cliente" => 1,
        "fecha_hora" => "2026-03-15 11:00:00",
        "id_estado" => 2,
        "observaciones" => "Visita actualizada con nueva hora y estado"
    ];

    const VISITA_PATCH = [
        "fecha_hora" => "2026-03-15 12:00:00",
        "observaciones" => "Visita parcialmente actualizada con nueva hora"
    ];
}
