<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';
require_once __DIR__ . '/../Fixture/ClienteData.php';
require_once __DIR__ . '/../Fixture/InvalidData.php';

use App\Models\User;
use tests\Fixture\ClienteData;
use tests\Fixture\InvalidData;
use tests\Fixture\LoginData;
use Tests\TestCase;

class ClienteTest extends TestCase
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

    // ========== LECTURA ==========
    public function test_get_all_clientes(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/clientes');

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

    public function test_get_cliente_by_id(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/clientes/1');

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

    public function test_get_cliente_not_found(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/clientes/9999');

        $response->assertStatus(404);
    }

    public function test_admin_sees_clientes_of_subordinated_zones(): void
    {
        $admin = User::where('email', LoginData::LOGIN_ADMIN['email'])->first();
        $zoneIds = $admin->subordinados()->pluck('id_zona')->filter()->toArray();
        if ($admin->id_zona) {
            $zoneIds[] = $admin->id_zona;
        }
        $zoneIds = array_unique($zoneIds);

        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/clientes');

        $response->assertStatus(200);
        $clientes = $response->json();
        $this->assertNotEmpty($clientes);
        foreach ($clientes as $cliente) {
            $this->assertNotEmpty($cliente['edificios']);
            $this->assertTrue(
                collect($cliente['edificios'])->contains(fn ($edificio) => in_array($edificio['id_zona'], $zoneIds, true)),
                'Cada cliente debe pertenecer a una zona subordinada del admin'
            );
        }
    }

    public function test_comercial_sees_only_clientes_in_own_zone(): void
    {
        $comercial = User::where('email', LoginData::LOGIN_COMERCIAL['email'])->first();

        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/clientes');

        $response->assertStatus(200);
        $clientes = $response->json();
        $this->assertNotEmpty($clientes);
        foreach ($clientes as $cliente) {
            $this->assertNotEmpty($cliente['edificios']);
            $this->assertTrue(
                collect($cliente['edificios'])->contains(fn ($edificio) => $edificio['id_zona'] === $comercial->id_zona),
                'Cada cliente debe pertenecer a la zona del comercial'
            );
        }
    }

    public function test_unauthenticated_cannot_get_clientes(): void
    {
        $response = $this->getJson('/api/clientes');
        $response->assertStatus(401);
    }

    // ========== CREACIÓN - EXITOSO ==========
    public function test_create_cliente_successfully(): void
    {
        $token = $this->adminToken();
        $cliente = ClienteData::CLIENTE_POST;
        $cliente['email'] = 'cliente.' . time() . '@example.com';

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/clientes', $cliente);

        $response->assertStatus(201)
            ->assertJsonPath('nombre', $cliente['nombre'])
            ->assertJsonPath('apellidos', $cliente['apellidos'])
            ->assertJsonPath('telefono', $cliente['telefono'])
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

    // ========== CREACIÓN - VALIDACIONES ==========
    public function test_create_cliente_nombre_required(): void
    {
        $token = $this->adminToken();
        $cliente = ClienteData::CLIENTE_POST;
        unset($cliente['nombre']);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/clientes', $cliente);

        $response->assertStatus(422)
            ->assertJsonPath('errors.nombre', ['El campo nombre es obligatorio']);
    }

    public function test_create_cliente_nombre_too_long(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/clientes', InvalidData::CLIENTE_NOMBRE_TOO_LONG);

        $response->assertStatus(422)
            ->assertJsonPath('errors.nombre', ['El campo nombre no debe superar 50 caracteres']);
    }

    public function test_create_cliente_apellidos_too_long(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/clientes', InvalidData::CLIENTE_APELLIDOS_TOO_LONG);

        $response->assertStatus(422)
            ->assertJsonPath('errors.apellidos', ['El campo apellidos no debe superar 100 caracteres']);
    }

    public function test_create_cliente_email_invalid(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/clientes', InvalidData::CLIENTE_EMAIL_INVALID);

        $response->assertStatus(422)
            ->assertJsonPath('errors.email', ['El campo email debe ser una dirección válida']);
    }

    public function test_create_cliente_email_too_long(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/clientes', InvalidData::CLIENTE_EMAIL_TOO_LONG);

        $response->assertStatus(422);
    }

    public function test_create_cliente_telefono_too_long(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/clientes', InvalidData::CLIENTE_TELEFONO_TOO_LONG);

        $response->assertStatus(422)
            ->assertJsonPath('errors.telefono', ['El campo telefono no debe superar 15 caracteres']);
    }

    public function test_create_cliente_comercial_forbidden(): void
    {
        $token = $this->comercialToken();
        $cliente = ClienteData::CLIENTE_POST;
        $cliente['email'] = 'cliente.' . time() . '@example.com';

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/clientes', $cliente);

        $response->assertStatus(403);
    }

    // ========== ACTUALIZACIÓN - EXITOSO ==========
    public function test_update_cliente_with_put(): void
    {
        $token = $this->adminToken();
        $clienteId = $this->createCliente($token);

        $cliente = ClienteData::CLIENTE_PUT;
        $cliente['email'] = 'cliente.put.' . uniqid() . '@example.com';

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/clientes/' . $clienteId, $cliente);

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

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->patchJson('/api/clientes/' . $clienteId, $clientePatch);

        $response->assertStatus(200)
            ->assertJsonPath('nombre', $clientePatch['nombre'])
            ->assertJsonPath('telefono', $clientePatch['telefono']);
    }

    // ========== ACTUALIZACIÓN - VALIDACIONES ==========
    public function test_update_cliente_nombre_too_long(): void
    {
        $token = $this->adminToken();
        $clienteId = $this->createCliente($token);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/clientes/' . $clienteId, InvalidData::CLIENTE_NOMBRE_TOO_LONG);

        $response->assertStatus(422)
            ->assertJsonPath('errors.nombre', ['El campo nombre no debe superar 50 caracteres']);
    }

    public function test_update_cliente_email_invalid(): void
    {
        $token = $this->adminToken();
        $clienteId = $this->createCliente($token);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/clientes/' . $clienteId, InvalidData::CLIENTE_EMAIL_INVALID);

        $response->assertStatus(422);
    }

    public function test_update_cliente_not_found(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/clientes/9999', ClienteData::CLIENTE_PUT);

        $response->assertStatus(404);
    }

    public function test_update_cliente_comercial_forbidden(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/clientes/1', ClienteData::CLIENTE_PUT);

        $response->assertStatus(403);
    }

    // ========== ELIMINACIÓN ==========
    public function test_delete_cliente_successfully(): void
    {
        $token = $this->adminToken();
        $clienteId = $this->createCliente($token);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/clientes/' . $clienteId);

        $response->assertStatus(204);

        // Verificar que el cliente se eliminó
        $getResponse = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/clientes/' . $clienteId);
        $getResponse->assertStatus(404);
    }

    public function test_delete_cliente_not_found(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/clientes/9999');

        $response->assertStatus(404);
    }

    public function test_delete_cliente_comercial_forbidden(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/clientes/1');

        $response->assertStatus(403);
    }
}
