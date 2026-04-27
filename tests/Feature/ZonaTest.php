<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';
require_once __DIR__ . '/../Fixture/ZonaData.php';
require_once __DIR__ . '/../Fixture/InvalidData.php';

use tests\Fixture\InvalidData;
use tests\Fixture\LoginData;
use tests\Fixture\ZonaData;
use Tests\TestCase;

class ZonaTest extends TestCase
{
    private function adminToken(): string
    {
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);
        return $loginResponse->json('access_token');
    }

    private function comercialToken(): string
    {
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_COMERCIAL);
        return $loginResponse->json('access_token');
    }

    private function createZona(string $token): int
    {
        $zona = ZonaData::ZONA_POST;
        $zona['nombre'] = 'Zona Base ' . uniqid();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/zonas', $zona);

        $response->assertStatus(201);
        return $response->json('id');
    }

    // ========== LECTURA ==========
    public function test_get_all_zonas(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/zonas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'nombre',
                    'area',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_comercial_can_get_all_zonas(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/zonas');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_cannot_get_zonas(): void
    {
        $response = $this->getJson('/api/zonas');
        $response->assertStatus(401);
    }

    public function test_get_zonas_index_returns_zonas(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/zonas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'nombre',
                    'area',
                ],
            ]);
    }

    // ========== CREACIÓN - EXITOSO ==========
    public function test_admin_can_create_zona_successfully(): void
    {
        $token = $this->adminToken();
        $zona = ZonaData::ZONA_POST;
        $zona['nombre'] = 'Zona Test ' . time();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/zonas', $zona);

        $response->assertStatus(201)
            ->assertJsonPath('nombre', $zona['nombre'])
            ->assertJsonStructure([
                'id',
                'nombre',
                'area',
                'created_at',
                'updated_at',
            ]);
    }

    // ========== CREACIÓN - VALIDACIONES ==========
    public function test_create_zona_nombre_required(): void
    {
        $token = $this->adminToken();
        $zona = ZonaData::ZONA_POST;
        unset($zona['nombre']);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/zonas', $zona);

        $response->assertStatus(422)
            ->assertJsonPath('errors.nombre', ['El campo nombre es obligatorio']);
    }

    public function test_create_zona_area_required(): void
    {
        $token = $this->adminToken();
        $zona = ZonaData::ZONA_POST;
        unset($zona['area']);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/zonas', $zona);

        $response->assertStatus(422)
            ->assertJsonPath('errors.area', ['El campo area es obligatorio']);
    }

    public function test_create_zona_comercial_forbidden(): void
    {
        $token = $this->comercialToken();
        $zona = ZonaData::ZONA_POST;
        $zona['nombre'] = 'Zona Test ' . time();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/zonas', $zona);

        $response->assertStatus(403);
    }

    // ========== ACTUALIZACIÓN - EXITOSO ==========
    public function test_admin_can_update_zona_with_put(): void
    {
        $token = $this->adminToken();
        $zonaId = $this->createZona($token);

        $zona = ZonaData::ZONA_PUT;
        $zona['nombre'] = 'Zona PUT ' . uniqid();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/zonas/' . $zonaId, $zona);

        $response->assertStatus(200)
            ->assertJsonPath('nombre', $zona['nombre']);
    }

    public function test_admin_can_update_zona_with_patch(): void
    {
        $token = $this->adminToken();
        $zonaId = $this->createZona($token);

        $zonaPatch = [
            'nombre' => 'Zona PATCH ' . uniqid(),
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->patchJson('/api/zonas/' . $zonaId, $zonaPatch);

        $response->assertStatus(200)
            ->assertJsonPath('nombre', $zonaPatch['nombre']);
    }

    // ========== ACTUALIZACIÓN - VALIDACIONES ==========
    public function test_update_zona_not_found(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/zonas/9999', ZonaData::ZONA_PUT);

        $response->assertStatus(404);
    }

    public function test_update_zona_comercial_forbidden(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/zonas/1', ZonaData::ZONA_PUT);

        $response->assertStatus(403);
    }

    // ========== ELIMINACIÓN ==========
    public function test_admin_can_delete_zona(): void
    {
        $token = $this->adminToken();
        $zonaId = $this->createZona($token);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/zonas/' . $zonaId);

        $response->assertStatus(204);

        // Verificar que desaparece de la lista de zonas
        $listResponse = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/zonas');
        $listResponse->assertStatus(200);

        $zonas = $listResponse->json();
        $this->assertNotContains($zonaId, array_column($zonas, 'id'));
    }

    public function test_delete_zona_not_found(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/zonas/9999');

        $response->assertStatus(404);
    }

    public function test_delete_zona_comercial_forbidden(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/zonas/1');

        $response->assertStatus(403);
    }

    // ========== GEOSPATIAL ENDPOINTS ==========
    public function test_get_zonas_mapa_datos(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/zonas/mapa');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'zonas' => [
                    '*' => [
                        'id',
                        'nombre',
                        'area',
                        'edificios' => [
                            '*' => [
                                'id',
                                'direccion_completa',
                                'ubicacion' => ['lat', 'lng'],
                                'tipo',
                                'clientes',
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function test_get_zonas_pagina_datos(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/zonas/pagina/datos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'nombre',
                    'area',
                    'edificios' => [
                        '*' => [
                            'id',
                            'direccion_completa',
                            'ubicacion',
                            'tipo',
                            'clientes' => [
                                '*' => [
                                    'id',
                                    'nombre',
                                    'apellidos',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
    }
}
