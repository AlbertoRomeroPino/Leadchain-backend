<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'LeadChain API',
    description: 'API REST de LeadChain para la gestión comercial de clientes, edificios y visitas. Autenticación mediante JWT (Bearer token). Incluye soporte para coordenadas geográficas PostGIS.',
    contact: new OA\Contact(name: 'Alberto Romero Pino', email: 'albertoromeropino2004@gmail.com')
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Servidor local'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
#[OA\Schema(
    schema: 'GeoPoint',
    required: ['lat', 'lng'],
    properties: [
        new OA\Property(property: 'lat', type: 'number', format: 'float', example: 37.90),
        new OA\Property(property: 'lng', type: 'number', format: 'float', example: -4.80),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ZonaInput',
    required: ['nombre', 'area'],
    properties: [
        new OA\Property(property: 'nombre', type: 'string', example: 'Zona Norte'),
        new OA\Property(
            property: 'area',
            type: 'array',
            minItems: 4,
            items: new OA\Items(ref: '#/components/schemas/GeoPoint')
        ),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ClienteResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nombre', type: 'string', example: 'Antonio'),
        new OA\Property(property: 'apellidos', type: 'string', example: 'Perez Garcia'),
        new OA\Property(property: 'telefono', type: 'string', example: '612345678'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'antonio@ejemplo.com'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'EdificioResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'direccion_completa', type: 'string', example: 'Calle Gran Capitan 10'),
        new OA\Property(property: 'ubicacion', ref: '#/components/schemas/GeoPoint'),
        new OA\Property(property: 'id_zona', type: 'integer', example: 1),
        new OA\Property(property: 'tipo', type: 'string', example: 'residencial'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'UserResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nombre', type: 'string', example: 'Alejandro'),
        new OA\Property(property: 'apellidos', type: 'string', example: 'Garcia Martinez'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ale.garcia@email.com'),
        new OA\Property(property: 'rol', type: 'string', example: 'comercial'),
        new OA\Property(property: 'id_responsable', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'id_zona', type: 'integer', nullable: true, example: 2),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'VisitaResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'id_usuario', type: 'integer', example: 2),
        new OA\Property(property: 'id_cliente', type: 'integer', example: 1),
        new OA\Property(property: 'fecha_hora', type: 'string', format: 'date-time', example: '2026-03-15 10:30:00'),
        new OA\Property(property: 'id_estado', type: 'integer', example: 1),
        new OA\Property(property: 'observaciones', type: 'string', nullable: true, example: 'Primera visita al cliente'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ZonaResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nombre', type: 'string', example: 'Zona Norte'),
        new OA\Property(property: 'area', type: 'array', items: new OA\Items(ref: '#/components/schemas/GeoPoint')),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ClienteDetalleResource',
    properties: [
        new OA\Property(
            property: 'cliente',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'nombre', type: 'string', example: 'Antonio'),
                new OA\Property(property: 'apellidos', type: 'string', example: 'Perez Garcia'),
                new OA\Property(property: 'telefono', type: 'string', nullable: true, example: '612345678'),
                new OA\Property(property: 'email', type: 'string', nullable: true, format: 'email', example: 'antonio@ejemplo.com'),
            ],
            type: 'object'
        ),
        new OA\Property(
            property: 'edificio',
            nullable: true,
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 10),
                new OA\Property(property: 'direccion_completa', type: 'string', example: 'Calle Cruz Conde 15, Córdoba'),
                new OA\Property(property: 'ubicacion', ref: '#/components/schemas/GeoPoint', nullable: true),
                new OA\Property(property: 'tipo', type: 'string', example: 'residencial'),
                new OA\Property(property: 'id_zona', type: 'integer', example: 3),
                new OA\Property(
                    property: 'zona',
                    nullable: true,
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 3),
                        new OA\Property(property: 'nombre', type: 'string', example: 'Centro'),
                    ],
                    type: 'object'
                ),
            ],
            type: 'object'
        ),
        new OA\Property(
            property: 'visitas',
            properties: [
                new OA\Property(property: 'total', type: 'integer', example: 12),
                new OA\Property(
                    property: 'ultima',
                    nullable: true,
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 44),
                        new OA\Property(property: 'id_usuario', type: 'integer', example: 5),
                        new OA\Property(property: 'id_cliente', type: 'integer', example: 1),
                        new OA\Property(property: 'id_estado', type: 'integer', example: 2),
                        new OA\Property(property: 'fecha_hora', type: 'string', format: 'date-time', example: '2026-03-20 09:30:00'),
                        new OA\Property(property: 'observaciones', type: 'string', nullable: true, example: 'Cliente no disponible, volver la próxima semana'),
                        new OA\Property(
                            property: 'estado',
                            nullable: true,
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 2),
                                new OA\Property(property: 'etiqueta', type: 'string', example: 'Pendiente'),
                                new OA\Property(property: 'color_hex', type: 'string', example: '#f59e0b'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(
                            property: 'usuario',
                            nullable: true,
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 5),
                                new OA\Property(property: 'nombre', type: 'string', example: 'Juan'),
                                new OA\Property(property: 'apellidos', type: 'string', example: 'García López'),
                            ],
                            type: 'object'
                        ),
                    ],
                    type: 'object'
                ),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'EstadoVisitaResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'etiqueta', type: 'string', example: 'Completada'),
        new OA\Property(property: 'color_hex', type: 'string', example: '#10b981'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'EdificioDetailResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'direccion_completa', type: 'string', example: 'Calle Gran Capitan 10'),
        new OA\Property(property: 'ubicacion', ref: '#/components/schemas/GeoPoint', nullable: true),
        new OA\Property(property: 'id_zona', type: 'integer', example: 1),
        new OA\Property(property: 'tipo', type: 'string', example: 'residencial'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'clientes', type: 'array', items: new OA\Items(ref: '#/components/schemas/ClienteResource')),
        new OA\Property(property: 'zona', ref: '#/components/schemas/ZonaResource', nullable: true),
        new OA\Property(property: 'bloqueEdificios', type: 'array', items: new OA\Items(ref: '#/components/schemas/EdificioDetailResource')),
        new OA\Property(property: 'todasLasZonas', type: 'array', items: new OA\Items(ref: '#/components/schemas/ZonaResource')),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'VisitasPaginaResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'id_usuario', type: 'integer', example: 2),
        new OA\Property(property: 'id_cliente', type: 'integer', example: 1),
        new OA\Property(property: 'fecha_hora', type: 'string', format: 'date-time', example: '2026-03-15 10:30:00'),
        new OA\Property(property: 'id_estado', type: 'integer', example: 1),
        new OA\Property(property: 'observaciones', type: 'string', nullable: true, example: 'Primera visita al cliente'),
        new OA\Property(property: 'estado', ref: '#/components/schemas/EstadoVisitaResource', nullable: true),
        new OA\Property(property: 'usuario', type: 'object', nullable: true),
        new OA\Property(property: 'cliente', ref: '#/components/schemas/ClienteResource', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ZonaPageResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nombre', type: 'string', example: 'Zona Norte'),
        new OA\Property(property: 'area', type: 'array', items: new OA\Items(ref: '#/components/schemas/GeoPoint')),
        new OA\Property(property: 'edificios', type: 'array', items: new OA\Items(type: 'object')),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
class OpenApiSpec {}
