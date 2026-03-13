<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';

use Tests\TestCase;
use tests\Fixture\LoginData;

class LoginTest extends TestCase
{
    /**
     * Test: Administrador logueado con credenciales válidas de las constantes de LoginData
     */
    public function test_admin_can_login_successfully(): void
    {
        $response = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login exitoso')
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonPath('user.email', LoginData::LOGIN_ADMIN['email'])
            ->assertJsonPath('user.rol', 'admin')
            ->assertJsonPath('dashboard', '/admin/dashboard');
    }

    /**
     * Test: Comercial logueado con credenciales válidas de las constantes de LoginData
     */
    public function test_comercial_can_login_successfully(): void
    {
        $response = $this->postJson('/api/auth/login', LoginData::LOGIN_COMERCIAL);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login exitoso')
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonPath('user.email', LoginData::LOGIN_COMERCIAL['email'])
            ->assertJsonPath('user.rol', 'comercial')
            ->assertJsonPath('dashboard', '/comercial/dashboard');
    }

    /**
     * Test: Cierre de sesión exitoso
     */
    public function test_user_can_logout_successfully(): void
    {
        // Primero, logueamos al usuario para obtener el token
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);
        $token = $loginResponse->json('access_token');

        // Luego, hacemos la solicitud de logout con el token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Sesión cerrada correctamente');
    }

    /**
     * Test: Refrescar token exitosamente
     */
    public function test_user_can_refresh_token_successfully(): void
    {
        // Primero, logueamos al usuario para obtener el token
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);
        $token = $loginResponse->json('access_token');

        // Luego, hacemos la solicitud de refresh con el token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login exitoso')
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonPath('user.email', LoginData::LOGIN_ADMIN['email'])
            ->assertJsonPath('user.rol', 'admin');
    }
    /**
     * Test: me retorna la información del usuario autenticado
     */
    public function test_user_can_get_authenticated_user_info(): void
    {
        // Primero, logueamos al usuario para obtener el token
        $loginResponse = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);
        $token = $loginResponse->json('access_token');

        // Luego, hacemos la solicitud de me con el token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('user.email', LoginData::LOGIN_ADMIN['email'])
            ->assertJsonPath('user.rol', 'admin')
            ->assertJsonPath('dashboard', '/admin/dashboard');
    }
}
