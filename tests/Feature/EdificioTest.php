<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';
require_once __DIR__ . '/../Fixture/EdificioData.php';

use tests\Fixture\EdificioData;
use tests\Fixture\LoginData;
use Tests\TestCase;

class EdificioTest extends TestCase
{
    private function adminToken(): string
    {
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);

        return $loginResponse->json('access_token');
    }

    public function test_get_all_edificios(): void
    {
        $token = $this->adminToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/edificios');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'direccion_completa',
                    'planta',
                    'puerta',
                    'ubicacion',
                    'id_zona',
                    'tipo',
                    'id_cliente',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_get_edificio_by_id(): void
    {
        $token = $this->adminToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/edificios/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'direccion_completa',
                'planta',
                'puerta',
                'ubicacion',
                'id_zona',
                'tipo',
                'id_cliente',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_create_edificio(): void
    {
        $token = $this->adminToken();
        $edificio = EdificioData::EDIFICIO_POST;
        $edificio['direccion_completa'] = 'Direccion Test ' . time();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/edificios', $edificio);

        $response->assertStatus(201)
            ->assertJsonPath('direccion_completa', $edificio['direccion_completa'])
            ->assertJsonPath('id_zona', $edificio['id_zona'])
            ->assertJsonPath('tipo', $edificio['tipo']);
    }

    public function test_update_edificio_with_put(): void
    {
        $token = $this->adminToken();
        $edificio = EdificioData::EDIFICIO_PUT;
        $edificio['direccion_completa'] = 'Direccion PUT ' . time();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/edificios/1', $edificio);

        $response->assertStatus(200)
            ->assertJsonPath('direccion_completa', $edificio['direccion_completa'])
            ->assertJsonPath('planta', $edificio['planta'])
            ->assertJsonPath('puerta', $edificio['puerta']);
    }

    public function test_update_edificio_with_patch(): void
    {
        $token = $this->adminToken();
        $edificioPatch = EdificioData::EDIFICIO_PATCH;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson('/api/edificios/1', $edificioPatch);

        $response->assertStatus(200)
            ->assertJsonPath('planta', $edificioPatch['planta'])
            ->assertJsonPath('puerta', $edificioPatch['puerta']);
    }

    public function test_delete_edificio(): void
    {
        $token = $this->adminToken();
        $edificio = EdificioData::EDIFICIO_POST;
        $edificio['direccion_completa'] = 'Direccion Delete ' . time();

        $edificioCreado = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/edificios', $edificio);

        $edificioId = $edificioCreado->json('id');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/edificios/' . $edificioId);

        $response->assertStatus(204);
    }
}
