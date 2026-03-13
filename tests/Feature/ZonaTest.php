<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';
require_once __DIR__ . '/../Fixture/ZonaData.php';

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

    private function createZona(string $token): int
    {
        $zona = ZonaData::ZONA_POST;
        $zona['nombre_zona'] = 'Zona Base ' . uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/zonas', $zona);

        $response->assertStatus(201);

        return $response->json('id');
    }

    public function test_get_all_zonas(): void
    {
        $token = $this->adminToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/zonas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'nombre_zona',
                    'esquina_noroeste' => ['lat', 'lng'],
                    'esquina_noreste' => ['lat', 'lng'],
                    'esquina_suroeste' => ['lat', 'lng'],
                    'esquina_sureste' => ['lat', 'lng'],
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_get_zona_by_id(): void
    {
        $token = $this->adminToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/zonas/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'nombre_zona',
                'esquina_noroeste' => ['lat', 'lng'],
                'esquina_noreste' => ['lat', 'lng'],
                'esquina_suroeste' => ['lat', 'lng'],
                'esquina_sureste' => ['lat', 'lng'],
                'created_at',
                'updated_at',
            ]);
    }

    public function test_create_zona(): void
    {
        $token = $this->adminToken();
        $zona = ZonaData::ZONA_POST;
        $zona['nombre_zona'] = 'Zona Test ' . time();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/zonas', $zona);

        $response->assertStatus(201)
            ->assertJsonPath('nombre_zona', $zona['nombre_zona']);
    }

    public function test_update_zona_with_put(): void
    {
        $token = $this->adminToken();
        $zonaId = $this->createZona($token);

        $zona = ZonaData::ZONA_PUT;
        $zona['nombre_zona'] = 'Zona PUT ' . uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/zonas/' . $zonaId, $zona);

        $response->assertStatus(200)
            ->assertJsonPath('nombre_zona', $zona['nombre_zona']);
    }

    public function test_update_zona_with_patch(): void
    {
        $token = $this->adminToken();
        $zonaId = $this->createZona($token);

        $zona = ZonaData::ZONA_PUT;
        $zona['nombre_zona'] = 'Zona PATCH ' . uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson('/api/zonas/' . $zonaId, $zona);

        $response->assertStatus(200)
            ->assertJsonPath('nombre_zona', $zona['nombre_zona']);
    }

    public function test_delete_zona(): void
    {
        $token = $this->adminToken();
        $zonaId = $this->createZona($token);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/zonas/' . $zonaId);

        $response->assertStatus(204);
    }
}
