<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';
require_once __DIR__ . '/../Fixture/UserData.php';

use tests\Fixture\LoginData;
use tests\Fixture\UserData;
use Tests\TestCase;

class UserTest extends TestCase
{
    private function adminToken(): string
    {
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);

        return $loginResponse->json('access_token');
    }

    private function createUser(string $token): int
    {
        $user = UserData::USER_POST;
        $user['email'] = 'user.base.' . uniqid() . '@email.com';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/users', $user);

        $response->assertStatus(201);

        return $response->json('id');
    }

    /**
     * Test: Sacar todos los usuarios
     */
    public function test_get_all_users(): void
    {
        $token = $this->adminToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'nombre',
                    'apellidos',
                    'email',
                    'rol',
                    'id_responsable',
                    'id_zona',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /**
     * Test: Sacar un usuario específico por ID
     */
    public function test_get_user_by_id(): void
    {
        $token = $this->adminToken();

        // ID del usuario que queremos obtener (el admin tiene ID 1)
        $userId = 1;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/users/{$userId}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'nombre',
                'apellidos',
                'email',
                'rol',
                'id_responsable',
                'id_zona',
                'created_at',
                'updated_at'
            ]);
    }

    /**
     * Test: Crear un nuevo usuario
     */
    public function test_create_user(): void
    {
        $token = $this->adminToken();

        $usuario = UserData::USER_POST;
        $usuario['email'] = 'user.create.' . uniqid() . '@email.com';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/users', $usuario);

        $response->assertStatus(201)
            ->assertJsonPath('nombre', $usuario['nombre'])
            ->assertJsonPath('email', $usuario['email'])
            ->assertJsonPath('rol', $usuario['rol'])
            ->assertJsonStructure([
                'id',
                'nombre',
                'apellidos',
                'email',
                'rol',
                'id_responsable',
                'id_zona',
                'created_at',
                'updated_at'
            ]);
    }

    /**
     * Test: Crear un administrador sin zona (usuario de oficina)
     */
    public function test_create_admin_without_zone(): void
    {
        $token = $this->adminToken();

        $usuario = UserData::USER_POST;
        $usuario['email'] = 'admin.office.' . uniqid() . '@email.com';
        $usuario['rol'] = 'admin';
        $usuario['id_zona'] = null;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/users', $usuario);

        $response->assertStatus(201)
            ->assertJsonPath('rol', 'admin')
            ->assertJsonPath('id_zona', null);
    }

    /**
     * Test: No permitir usuario no admin sin zona
     */
    public function test_create_non_admin_without_zone_fails(): void
    {
        $token = $this->adminToken();

        $usuario = UserData::USER_POST;
        $usuario['email'] = 'comercial.nozona.' . uniqid() . '@email.com';
        $usuario['rol'] = 'comercial';
        $usuario['id_zona'] = null;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/users', $usuario);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id_zona']);
    }

    /**
     * Test: Actualizar un usuario existente
     */
    public function test_update_user(): void
    {
        $token = $this->adminToken();
        $userId = $this->createUser($token);

        $usuario = UserData::USER_PUT;
        $usuario['email'] = 'update.user.' . uniqid() . '@email.com';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson('/api/users/' . $userId, $usuario);

        $response->assertStatus(200)
            ->assertJsonPath('nombre', $usuario['nombre'])
            ->assertJsonPath('email', $usuario['email'])
            ->assertJsonPath('rol', $usuario['rol'])
            ->assertJsonStructure([
                'id',
                'nombre',
                'apellidos',
                'email',
                'rol',
                'id_responsable',
                'id_zona',
                'created_at',
                'updated_at'
            ]);
    }

    /**
     * Test: Actualizar parcialmente un usuario creado en el propio test
     */
    public function test_patch_user(): void
    {
        $token = $this->adminToken();
        $userId = $this->createUser($token);

        $usuario = UserData::USER_PATCH;
        $usuario['email'] = 'patch.user.' . uniqid() . '@email.com';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->patchJson('/api/users/' . $userId, $usuario);

        $response->assertStatus(200)
            ->assertJsonPath('nombre', $usuario['nombre'])
            ->assertJsonPath('email', $usuario['email']);
    }

    /**
     * Test: Permitir dejar sin zona cuando se actualiza a admin
     */
    public function test_patch_user_to_admin_without_zone(): void
    {
        $token = $this->adminToken();
        $userId = $this->createUser($token);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->patchJson('/api/users/' . $userId, [
            'rol' => 'admin',
            'id_zona' => null,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('rol', 'admin')
            ->assertJsonPath('id_zona', null);
    }

    /**
     * Test: Bloquear quitar zona a usuario no admin
     */
    public function test_patch_non_admin_without_zone_fails(): void
    {
        $token = $this->adminToken();
        $userId = $this->createUser($token);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->patchJson('/api/users/' . $userId, [
            'id_zona' => null,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id_zona']);
    }

    /**
     * Test: Eliminar un usuario
     */
    public function test_delete_user(): void
    {
        $token = $this->adminToken();
        $userId = $this->createUser($token);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/users/{$userId}");

        $response->assertStatus(204);
    }
}
