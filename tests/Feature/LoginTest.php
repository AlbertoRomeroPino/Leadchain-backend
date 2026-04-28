<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';
require_once __DIR__ . '/../Fixture/InvalidData.php';

use tests\Fixture\InvalidData;
use Tests\TestCase;
use tests\Fixture\LoginData;

class LoginTest extends TestCase
{
    // ========== AUTENTICACIÓN - EXITOSA ==========
    public function test_admin_can_login_successfully(): void
    {
        $response = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login exitoso')
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonPath('user.email', LoginData::LOGIN_ADMIN['email'])
            ->assertJsonPath('user.rol', 'admin')
            ->assertJsonPath('inicio', '/admin/dashboard')
            ->assertJsonStructure([
                'success',
                'message',
                'access_token',
                'token_type',
                'expires_in',
                'user' => [
                    'id',
                    'nombre',
                    'apellidos',
                    'email',
                    'rol',
                ],
                'inicio',
            ]);
    }

    public function test_comercial_can_login_successfully(): void
    {
        $response = $this->postJson('/api/auth/login', LoginData::LOGIN_COMERCIAL);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login exitoso')
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonPath('user.email', LoginData::LOGIN_COMERCIAL['email'])
            ->assertJsonPath('user.rol', 'comercial')
            ->assertJsonPath('inicio', '/comercial/dashboard');
    }

    public function test_admin_can_get_authenticated_user_info(): void
    {
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);
        $token = $loginResponse->json('access_token');

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('user.email', LoginData::LOGIN_ADMIN['email'])
            ->assertJsonPath('user.rol', 'admin')
            ->assertJsonPath('inicio', '/admin/dashboard');
    }

    // ========== AUTENTICACIÓN - VALIDACIONES ==========
    public function test_login_email_required(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'password' => 'password123'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.email', ['El campo email es obligatorio']);
    }

    public function test_login_password_required(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'root@leadchain.com'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.password', ['El campo password es obligatorio']);
    }

    public function test_login_email_invalid_format(): void
    {
        $response = $this->postJson('/api/auth/login', InvalidData::LOGIN_EMAIL_INVALID);

        $response->assertStatus(422)
            ->assertJsonPath('errors.email', ['El campo email debe ser una dirección válida']);
    }

    public function test_login_email_nonexistent(): void
    {
        $response = $this->postJson('/api/auth/login', InvalidData::LOGIN_EMAIL_NONEXISTENT);

        $response->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Credenciales inválidas');
    }

    public function test_login_password_wrong(): void
    {
        $response = $this->postJson('/api/auth/login', InvalidData::LOGIN_PASSWORD_WRONG);

        $response->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Credenciales inválidas');
    }

    // ========== SESIÓN - OPERACIONES ==========
    public function test_user_can_logout_successfully(): void
    {
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);
        $token = $loginResponse->json('access_token');

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Sesión cerrada correctamente');
    }

    public function test_logout_without_token(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    public function test_user_can_refresh_token_successfully(): void
    {
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);
        $token = $loginResponse->json('access_token');

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login exitoso')
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonPath('user.email', LoginData::LOGIN_ADMIN['email'])
            ->assertJsonPath('user.rol', 'admin');
    }

    public function test_refresh_without_token(): void
    {
        $response = $this->postJson('/api/auth/refresh');

        $response->assertStatus(401);
    }

    public function test_me_without_token(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    public function test_me_with_invalid_token(): void
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer invalid_token'])
            ->getJson('/api/auth/me');

        $response->assertStatus(401);
    }
}
