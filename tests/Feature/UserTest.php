<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';
require_once __DIR__ . '/../Fixture/UserData.php';

use tests\Fixture\LoginData;
use tests\Fixture\UserData;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test: Sacar todos los usuarios
     */
    public function test_get_all_users(): void
    {
        // Primero, logueamos al usuario para obtener el token
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);
        $token = $loginResponse->json('access_token');

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
        // Primero, logueamos al usuario para obtener el token
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);
        $token = $loginResponse->json('access_token');

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
        // Primero, logueamos al usuario para obtener el token
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);
        $token = $loginResponse->json('access_token');

        $usuario = UserData::USER_POST;

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
     * Test: Actualizar un usuario existente
     */
    public function test_update_user(): void
    {
        // Primero, logueamos al usuario para obtener el token
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);
        $token = $loginResponse->json('access_token');

        $usuario = UserData::USER_PUT;
        $usuario['email'] = 'update.user.' . time() . '@email.com';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson('/api/users/2', $usuario);

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
     * Test: Eliminar un usuario
     */
    public function test_delete_user(): void
    {
        // Primero, logueamos al usuario para obtener el token
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);
        $token = $loginResponse->json('access_token');

        // ID del usuario que queremos eliminar (el admin tiene ID 1)
        $userId = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/users', [
            'nombre' => 'Usuario a eliminar',
            'apellidos' => 'Test',
            'email' => 'usuario_eliminar.' . time() . '@example.com',
            'password' => 'password1234',
            'rol' => 'comercial',
        ])->json('id');

        $this->assertNotNull($userId);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/users/{$userId}");

        $response->assertStatus(204);
    }
}
