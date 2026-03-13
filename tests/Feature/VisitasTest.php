<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';
require_once __DIR__ . '/../Fixture/VisitasData.php';

use tests\Fixture\LoginData;
use tests\Fixture\VisitasData;
use Tests\TestCase;

class VisitasTest extends TestCase
{
    private function adminToken(): string
    {
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);

        return $loginResponse->json('access_token');
    }

    private function createVisita(string $token): int
    {
        $visita = VisitasData::VISITA_POST;
        $visita['observaciones'] = 'Visita Base ' . uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/visitas', $visita);

        $response->assertStatus(201);

        return $response->json('id');
    }

    public function test_get_all_visitas(): void
    {
        $token = $this->adminToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/visitas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'id_usuario',
                    'id_cliente',
                    'fecha_hora',
                    'id_estado',
                    'observaciones',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_get_visita_by_id(): void
    {
        $token = $this->adminToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/visitas/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'id_usuario',
                'id_cliente',
                'fecha_hora',
                'id_estado',
                'observaciones',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_create_visita(): void
    {
        $token = $this->adminToken();
        $visita = VisitasData::VISITA_POST;
        $visita['observaciones'] = 'Creacion visita ' . time();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/visitas', $visita);

        $response->assertStatus(201)
            ->assertJsonPath('id_usuario', $visita['id_usuario'])
            ->assertJsonPath('id_cliente', $visita['id_cliente'])
            ->assertJsonPath('id_estado', $visita['id_estado']);
    }

    public function test_update_visita_with_put(): void
    {
        $token = $this->adminToken();
        $visitaId = $this->createVisita($token);

        $visita = VisitasData::VISITA_PUT;
        $visita['observaciones'] = 'PUT visita ' . uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/visitas/' . $visitaId, $visita);

        $response->assertStatus(200)
            ->assertJsonPath('id_estado', $visita['id_estado'])
            ->assertJsonPath('observaciones', $visita['observaciones']);
    }

    public function test_update_visita_with_patch(): void
    {
        $token = $this->adminToken();
        $visitaId = $this->createVisita($token);

        $visita = VisitasData::VISITA_PUT;
        $visita['id_estado'] = 3;
        $visita['observaciones'] = 'PATCH visita ' . uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson('/api/visitas/' . $visitaId, $visita);

        $response->assertStatus(200)
            ->assertJsonPath('id_estado', $visita['id_estado'])
            ->assertJsonPath('observaciones', $visita['observaciones']);
    }

    public function test_delete_visita(): void
    {
        $token = $this->adminToken();
        $visitaId = $this->createVisita($token);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/visitas/' . $visitaId);

        $response->assertStatus(204);
    }
}
