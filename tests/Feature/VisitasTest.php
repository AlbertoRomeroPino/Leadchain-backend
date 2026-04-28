<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';
require_once __DIR__ . '/../Fixture/VisitasData.php';
require_once __DIR__ . '/../Fixture/InvalidData.php';

use tests\Fixture\InvalidData;
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

    private function comercialToken(): string
    {
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_COMERCIAL);
        return $loginResponse->json('access_token');
    }

    private function createVisita(string $token = null): int
    {
        $token = $token ?? $this->comercialToken();
        $visita = VisitasData::VISITA_POST;
        $visita['observaciones'] = 'Visita Base ' . uniqid();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/visitas', $visita);

        $response->assertStatus(201);
        return $response->json('id');
    }

    // ========== LECTURA ==========
    public function test_get_visitas_pagina_datos_consolidados(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/visitas/pagina/datos-consolidados');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'visitas',
                'clientes',
                'estados',
            ]);
    }

    public function test_unauthenticated_cannot_get_visitas(): void
    {
        $response = $this->getJson('/api/visitas/pagina/datos-consolidados');
        $response->assertStatus(401);
    }

    // ========== CREACIÓN - EXITOSO ==========
    public function test_comercial_can_create_visita_successfully(): void
    {
        $token = $this->comercialToken();
        $visita = VisitasData::VISITA_POST;
        $visita['observaciones'] = 'Creacion visita ' . time();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/visitas', $visita);

        $response->assertStatus(201)
            ->assertJsonPath('id_usuario', $visita['id_usuario'])
            ->assertJsonPath('id_cliente', $visita['id_cliente'])
            ->assertJsonPath('id_estado', $visita['id_estado'])
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

    // ========== CREACIÓN - VALIDACIONES ==========
    public function test_create_visita_fecha_required(): void
    {
        $token = $this->comercialToken();
        $visita = VisitasData::VISITA_POST;
        unset($visita['fecha_hora']);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/visitas', $visita);

        $response->assertStatus(422)
            ->assertJsonPath('errors.fecha_hora', ['El campo fecha_hora es obligatorio']);
    }

    public function test_create_visita_fecha_invalid(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/visitas', InvalidData::VISITA_FECHA_EMPTY);

        $response->assertStatus(422);
    }

    public function test_create_visita_estado_nonexistent(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/visitas', InvalidData::VISITA_ESTADO_NONEXISTENT);

        $response->assertStatus(422);
    }

    public function test_create_visita_usuario_nonexistent(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/visitas', InvalidData::VISITA_USUARIO_NONEXISTENT);

        $response->assertStatus(422);
    }

    public function test_create_visita_cliente_nonexistent(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/visitas', InvalidData::VISITA_CLIENTE_NONEXISTENT);

        $response->assertStatus(422);
    }

    public function test_admin_cannot_create_visita(): void
    {
        $token = $this->adminToken();
        $visita = VisitasData::VISITA_POST;
        $visita['observaciones'] = 'Admin trying to create ' . time();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/visitas', $visita);

        $response->assertStatus(403);
    }

    // ========== ACTUALIZACIÓN - EXITOSO ==========
    public function test_comercial_can_update_visita_with_put(): void
    {
        $token = $this->comercialToken();
        $visitaId = $this->createVisita($token);

        $visita = VisitasData::VISITA_PUT;
        $visita['observaciones'] = 'PUT visita ' . uniqid();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/visitas/' . $visitaId, $visita);

        $response->assertStatus(200)
            ->assertJsonPath('id_estado', $visita['id_estado'])
            ->assertJsonPath('observaciones', $visita['observaciones']);
    }

    public function test_comercial_can_update_visita_with_patch(): void
    {
        $token = $this->comercialToken();
        $visitaId = $this->createVisita($token);

        $visitaPatch = [
            'id_estado' => 3,
            'observaciones' => 'PATCH visita ' . uniqid(),
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->patchJson('/api/visitas/' . $visitaId, $visitaPatch);

        $response->assertStatus(200)
            ->assertJsonPath('id_estado', $visitaPatch['id_estado'])
            ->assertJsonPath('observaciones', $visitaPatch['observaciones']);
    }

    // ========== ACTUALIZACIÓN - VALIDACIONES ==========
    public function test_update_visita_estado_nonexistent(): void
    {
        $token = $this->comercialToken();
        $visitaId = $this->createVisita($token);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->patchJson('/api/visitas/' . $visitaId, ['id_estado' => 9999]);

        $response->assertStatus(422);
    }

    public function test_update_visita_not_found(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/visitas/9999', VisitasData::VISITA_PUT);

        $response->assertStatus(404);
    }

    public function test_admin_cannot_update_visita(): void
    {
        $token = $this->comercialToken();
        $visitaId = $this->createVisita($token);

        $adminToken = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $adminToken])
            ->putJson('/api/visitas/' . $visitaId, VisitasData::VISITA_PUT);

        $response->assertStatus(403);
    }

    // ========== ELIMINACIÓN ==========
    public function test_admin_can_delete_visita(): void
    {
        $visitaId = $this->createVisita();
        $token = $this->adminToken();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/visitas/' . $visitaId);

        $response->assertStatus(204);

        // Verificar que la visita ya no aparece en la lista de visitas consolidadas
        $getResponse = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/visitas/pagina/datos-consolidados');

        $getResponse->assertStatus(200);
        $visitas = $getResponse->json('visitas');
        $this->assertNotContains($visitaId, array_column($visitas, 'id'));
    }

    public function test_delete_visita_not_found(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/visitas/9999');

        $response->assertStatus(404);
    }

    public function test_comercial_cannot_delete_visita(): void
    {
        $visitaId = $this->createVisita();
        $token = $this->comercialToken();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/visitas/' . $visitaId);

        $response->assertStatus(403);
    }
}
