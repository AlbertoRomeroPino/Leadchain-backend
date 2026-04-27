<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';
require_once __DIR__ . '/../Fixture/EdificioData.php';
require_once __DIR__ . '/../Fixture/InvalidData.php';

use tests\Fixture\EdificioData;
use tests\Fixture\InvalidData;
use tests\Fixture\LoginData;
use Tests\TestCase;

class EdificioTest extends TestCase
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

    private function createEdificio(string $token): int
    {
        $edificio = EdificioData::EDIFICIO_POST;
        $edificio['direccion_completa'] = 'Direccion Base ' . uniqid();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/edificios', $edificio);

        $response->assertStatus(201);
        return $response->json('id');
    }

    // ========== LECTURA ==========
    public function test_get_all_edificios(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/edificios');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'direccion_completa',
                    'ubicacion',
                    'id_zona',
                    'tipo',
                    'clientes',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_get_edificio_detalle_by_id(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/edificios/1/detalle');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'direccion_completa',
                'ubicacion',
                'id_zona',
                'tipo',
                'clientes',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_get_edificio_detalle_not_found(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/edificios/9999/detalle');

        $response->assertStatus(404);
    }

    public function test_get_edificio_ubicacion_structure(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/edificios/1/detalle');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'ubicacion' => [
                    'lat',
                    'lng',
                ],
            ]);
    }

    public function test_unauthenticated_cannot_get_edificios(): void
    {
        $response = $this->getJson('/api/edificios');
        $response->assertStatus(401);
    }

    // ========== CREACIÓN - EXITOSO ==========
    public function test_create_edificio_successfully(): void
    {
        $token = $this->adminToken();
        $edificio = EdificioData::EDIFICIO_POST;
        $edificio['direccion_completa'] = 'Direccion Test ' . time();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/edificios', $edificio);

        $response->assertStatus(201)
            ->assertJsonPath('direccion_completa', $edificio['direccion_completa'])
            ->assertJsonPath('id_zona', $edificio['id_zona'])
            ->assertJsonPath('tipo', $edificio['tipo'])
            ->assertJsonStructure([
                'id',
                'direccion_completa',
                'ubicacion' => ['lat', 'lng'],
                'id_zona',
                'tipo',
                'clientes',
                'created_at',
                'updated_at',
            ]);
    }

    // ========== CREACIÓN - VALIDACIONES ==========
    public function test_create_edificio_direccion_required(): void
    {
        $token = $this->adminToken();
        $edificio = EdificioData::EDIFICIO_POST;
        unset($edificio['direccion_completa']);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/edificios', $edificio);

        $response->assertStatus(422)
            ->assertJsonPath('errors.direccion_completa', ['El campo direccion_completa es obligatorio']);
    }

    public function test_create_edificio_direccion_too_long(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/edificios', InvalidData::EDIFICIO_DIRECCION_TOO_LONG);

        $response->assertStatus(422)
            ->assertJsonPath('errors.direccion_completa', ['El campo direccion_completa no debe superar 40 caracteres']);
    }

    public function test_create_edificio_tipo_too_long(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/edificios', InvalidData::EDIFICIO_TIPO_TOO_LONG);

        $response->assertStatus(422)
            ->assertJsonPath('errors.tipo', ['El campo tipo no debe superar 25 caracteres']);
    }

    public function test_create_edificio_ubicacion_lat_out_of_range(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/edificios', InvalidData::EDIFICIO_LAT_OUT_OF_RANGE);

        $response->assertStatus(422);
    }

    public function test_create_edificio_ubicacion_lng_out_of_range(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/edificios', InvalidData::EDIFICIO_LNG_OUT_OF_RANGE);

        $response->assertStatus(422);
    }

    public function test_create_edificio_zona_nonexistent(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/edificios', InvalidData::EDIFICIO_ZONA_NONEXISTENT);

        $response->assertStatus(422);
    }

    public function test_create_edificio_comercial_forbidden(): void
    {
        $token = $this->comercialToken();
        $edificio = EdificioData::EDIFICIO_POST;
        $edificio['direccion_completa'] = 'Direccion Test ' . time();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/edificios', $edificio);

        $response->assertStatus(403);
    }

    // ========== ACTUALIZACIÓN - EXITOSO ==========
    public function test_update_edificio_with_put(): void
    {
        $token = $this->adminToken();
        $edificioId = $this->createEdificio($token);

        $edificio = EdificioData::EDIFICIO_PUT;
        $edificio['direccion_completa'] = 'Direccion PUT ' . uniqid();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/edificios/' . $edificioId, $edificio);

        $response->assertStatus(200)
            ->assertJsonPath('direccion_completa', $edificio['direccion_completa'])
            ->assertJsonPath('id_zona', $edificio['id_zona'])
            ->assertJsonPath('tipo', $edificio['tipo']);
    }

    public function test_update_edificio_with_patch(): void
    {
        $token = $this->adminToken();
        $edificioId = $this->createEdificio($token);

        $edificioPatch = EdificioData::EDIFICIO_PATCH;
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->patchJson('/api/edificios/' . $edificioId, $edificioPatch);

        $response->assertStatus(200)
            ->assertJsonPath('id_zona', $edificioPatch['id_zona'])
            ->assertJsonPath('tipo', $edificioPatch['tipo']);
    }

    // ========== ACTUALIZACIÓN - VALIDACIONES ==========
    public function test_update_edificio_direccion_too_long(): void
    {
        $token = $this->adminToken();
        $edificioId = $this->createEdificio($token);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/edificios/' . $edificioId, InvalidData::EDIFICIO_DIRECCION_TOO_LONG);

        $response->assertStatus(422);
    }

    public function test_update_edificio_not_found(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/edificios/9999', EdificioData::EDIFICIO_PUT);

        $response->assertStatus(404);
    }

    public function test_update_edificio_comercial_forbidden(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/edificios/1', EdificioData::EDIFICIO_PUT);

        $response->assertStatus(403);
    }

    // ========== ELIMINACIÓN ==========
    public function test_delete_edificio_successfully(): void
    {
        $token = $this->adminToken();
        $edificioId = $this->createEdificio($token);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/edificios/' . $edificioId);

        $response->assertStatus(204);

        // Verificar que se eliminó
        $getResponse = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/edificios/' . $edificioId . '/detalle');
        $getResponse->assertStatus(404);
    }

    public function test_delete_edificio_not_found(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/edificios/9999');

        $response->assertStatus(404);
    }

    public function test_delete_edificio_comercial_forbidden(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/edificios/1');

        $response->assertStatus(403);
    }
}
