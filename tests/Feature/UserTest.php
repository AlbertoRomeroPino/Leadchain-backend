<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';
require_once __DIR__ . '/../Fixture/UserData.php';
require_once __DIR__ . '/../Fixture/InvalidData.php';

use tests\Fixture\InvalidData;
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

    private function comercialToken(): string
    {
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_COMERCIAL);
        return $loginResponse->json('access_token');
    }

    private function createUser(string $token): int
    {
        $user = UserData::USER_POST;
        $user['email'] = 'user.base.' . uniqid() . '@email.com';

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/users', $user);

        $response->assertStatus(201);
        return $response->json('id');
    }

    // ========== LECTURA ==========
    public function test_get_all_users(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/users');

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

    public function test_get_user_by_id(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/users/1');

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

    public function test_get_user_not_found(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/users/9999');

        $response->assertStatus(404);
    }

    public function test_comercial_cannot_get_all_users(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/users');

        $response->assertStatus(403);
    }

    // ========== CREACIÓN - EXITOSO ==========
    public function test_create_user_successfully(): void
    {
        $token = $this->adminToken();
        $usuario = UserData::USER_POST;
        $usuario['email'] = 'user.create.' . uniqid() . '@email.com';

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/users', $usuario);

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

    public function test_create_admin_without_zone(): void
    {
        $token = $this->adminToken();
        $usuario = UserData::USER_POST;
        $usuario['email'] = 'admin.office.' . uniqid() . '@email.com';
        $usuario['rol'] = 'admin';
        $usuario['id_zona'] = null;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/users', $usuario);

        $response->assertStatus(201)
            ->assertJsonPath('rol', 'admin')
            ->assertJsonPath('id_zona', null);
    }

    // ========== CREACIÓN - VALIDACIONES ==========
    public function test_create_user_nombre_required(): void
    {
        $token = $this->adminToken();
        $user = UserData::USER_POST;
        $user['email'] = 'user.' . uniqid() . '@email.com';
        unset($user['nombre']);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/users', $user);

        $response->assertStatus(422)
            ->assertJsonPath('errors.nombre', ['El campo nombre es obligatorio']);
    }

    public function test_create_user_nombre_too_long(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/users', InvalidData::USER_NOMBRE_TOO_LONG);

        $response->assertStatus(422)
            ->assertJsonPath('errors.nombre', ['El campo nombre no debe superar 50 caracteres']);
    }

    public function test_create_user_apellidos_too_long(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/users', InvalidData::USER_APELLIDOS_TOO_LONG);

        $response->assertStatus(422)
            ->assertJsonPath('errors.apellidos', ['El campo apellidos no debe superar 100 caracteres']);
    }

    public function test_create_user_email_required(): void
    {
        $token = $this->adminToken();
        $user = UserData::USER_POST;
        unset($user['email']);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/users', $user);

        $response->assertStatus(422)
            ->assertJsonPath('errors.email', ['El campo email es obligatorio']);
    }

    public function test_create_user_email_invalid(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/users', InvalidData::USER_EMAIL_INVALID);

        $response->assertStatus(422);
    }

    public function test_create_user_email_duplicate(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/users', InvalidData::USER_EMAIL_DUPLICATE);

        $response->assertStatus(422)
            ->assertJsonPath('errors.email', ['El campo email ya está en uso']);
    }

    public function test_create_user_password_required(): void
    {
        $token = $this->adminToken();
        $user = UserData::USER_POST;
        $user['email'] = 'user.' . uniqid() . '@email.com';
        unset($user['password']);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/users', $user);

        $response->assertStatus(422)
            ->assertJsonPath('errors.password', ['El campo password es obligatorio']);
    }

    public function test_create_non_admin_without_zone_fails(): void
    {
        $token = $this->adminToken();
        $usuario = UserData::USER_POST;
        $usuario['email'] = 'comercial.nozona.' . uniqid() . '@email.com';
        $usuario['rol'] = 'comercial';
        $usuario['id_zona'] = null;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/users', $usuario);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id_zona']);
    }

    public function test_create_user_comercial_forbidden(): void
    {
        $token = $this->comercialToken();
        $usuario = UserData::USER_POST;
        $usuario['email'] = 'user.' . uniqid() . '@email.com';

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/users', $usuario);

        $response->assertStatus(403);
    }

    // ========== ACTUALIZACIÓN - EXITOSO ==========
    public function test_update_user_successfully(): void
    {
        $token = $this->adminToken();
        $userId = $this->createUser($token);

        $usuario = UserData::USER_PUT;
        $usuario['email'] = 'update.user.' . uniqid() . '@email.com';

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/users/' . $userId, $usuario);

        $response->assertStatus(200)
            ->assertJsonPath('nombre', $usuario['nombre'])
            ->assertJsonPath('email', $usuario['email']);
    }

    public function test_patch_user_successfully(): void
    {
        $token = $this->adminToken();
        $userId = $this->createUser($token);

        $updateData = [
            'nombre' => 'NuevoNombre',
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->patchJson('/api/users/' . $userId, $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('nombre', 'NuevoNombre');
    }

    public function test_patch_user_to_admin_without_zone(): void
    {
        $token = $this->adminToken();
        $userId = $this->createUser($token);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->patchJson('/api/users/' . $userId, [
                'rol' => 'admin',
                'id_zona' => null,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('rol', 'admin')
            ->assertJsonPath('id_zona', null);
    }

    // ========== ACTUALIZACIÓN - VALIDACIONES ==========
    public function test_update_user_nombre_too_long(): void
    {
        $token = $this->adminToken();
        $userId = $this->createUser($token);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/users/' . $userId, InvalidData::USER_NOMBRE_TOO_LONG);

        $response->assertStatus(422)
            ->assertJsonPath('errors.nombre', ['El campo nombre no debe superar 50 caracteres']);
    }

    public function test_update_user_not_found(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/users/9999', UserData::USER_PUT);

        $response->assertStatus(404);
    }

    public function test_update_non_admin_without_zone_fails(): void
    {
        $token = $this->adminToken();
        $userId = $this->createUser($token);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->patchJson('/api/users/' . $userId, ['id_zona' => null]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id_zona']);
    }

    public function test_update_user_comercial_forbidden(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/users/1', UserData::USER_PUT);

        $response->assertStatus(403);
    }

    // ========== ELIMINACIÓN ==========
    public function test_delete_user_successfully(): void
    {
        $token = $this->adminToken();
        $userId = $this->createUser($token);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/users/' . $userId);

        $response->assertStatus(204);

        // Verificar que se eliminó
        $getResponse = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/users/' . $userId);
        $getResponse->assertStatus(404);
    }

    public function test_delete_user_not_found(): void
    {
        $token = $this->adminToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/users/9999');

        $response->assertStatus(404);
    }

    public function test_delete_user_comercial_forbidden(): void
    {
        $token = $this->comercialToken();
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson('/api/users/1');

        $response->assertStatus(403);
    }
}
