<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';
require_once __DIR__ . '/../Fixture/ClienteData.php';

use tests\Fixture\ClienteData;
use tests\Fixture\LoginData;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    private function adminToken(): string
    {
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);

        return $loginResponse->json('access_token');
    }

    private function createCliente(string $token): int
    {
        $cliente = ClienteData::CLIENTE_POST;
        $cliente['email'] = 'cliente.base.' . uniqid() . '@example.com';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/clientes', $cliente);

        $response->assertStatus(201);

        return $response->json('id');
    }

    public function test_get_all_clientes(): void
    {
        $token = $this->adminToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clientes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'nombre',
                    'apellidos',
                    'telefono',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    private function comercialToken(): string
    {
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'juan.garcia@leadchain.com',
            'password' => '12345678',
        ]);

        return $loginResponse->json('access_token');
    }

    public function test_get_cliente_by_id(): void
    {
        $token = $this->adminToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clientes/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'nombre',
                'apellidos',
                'telefono',
                'email',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_admin_sees_clientes_of_subordinated_zones(): void
    {
        $token = $this->adminToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clientes');

        $response->assertStatus(200)
            ->assertJsonCount(4)
            ->assertJsonFragment(['id' => 1])
            ->assertJsonFragment(['id' => 2])
            ->assertJsonFragment(['id' => 3])
            ->assertJsonFragment(['id' => 4]);
    }

    public function test_comercial_sees_only_clientes_in_own_zone(): void
    {
        $token = $this->comercialToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/clientes');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['id' => 1])
            ->assertJsonFragment(['id' => 4]);
    }

    public function test_create_cliente(): void
    {
        $token = $this->adminToken();
        $cliente = ClienteData::CLIENTE_POST;
        $cliente['email'] = 'cliente.' . time() . '@example.com';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/clientes', $cliente);

        $response->assertStatus(201)
            ->assertJsonPath('nombre', $cliente['nombre'])
            ->assertJsonPath('email', $cliente['email'])
            ->assertJsonStructure([
                'id',
                'nombre',
                'apellidos',
                'telefono',
                'email',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_update_cliente_with_put(): void
    {
        $token = $this->adminToken();
        $clienteId = $this->createCliente($token);

        $cliente = ClienteData::CLIENTE_PUT;
        $cliente['email'] = 'cliente.put.' . uniqid() . '@example.com';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/clientes/' . $clienteId, $cliente);

        $response->assertStatus(200)
            ->assertJsonPath('nombre', $cliente['nombre'])
            ->assertJsonPath('email', $cliente['email']);
    }

    public function test_update_cliente_with_patch(): void
    {
        $token = $this->adminToken();
        $clienteId = $this->createCliente($token);

        $clientePatch = [
            'nombre' => 'Cliente Patch',
            'telefono' => '699999999',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson('/api/clientes/' . $clienteId, $clientePatch);

        $response->assertStatus(200)
            ->assertJsonPath('nombre', $clientePatch['nombre'])
            ->assertJsonPath('telefono', $clientePatch['telefono']);
    }

    public function test_delete_cliente(): void
    {
        $token = $this->adminToken();
        $clienteId = $this->createCliente($token);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/clientes/' . $clienteId);

        $response->assertStatus(204);
    }
}
