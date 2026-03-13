<?php

namespace Tests\Feature;

require_once __DIR__ . '/../Fixture/LoginData.php';

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use tests\Fixture\LoginData;

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellidos', 150);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('rol', 50)->default('comercial');
            $table->unsignedBigInteger('id_responsable')->nullable();
            $table->unsignedBigInteger('id_zona')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function test_admin_can_login_successfully(): void
    {
        User::create([
            'nombre'   => 'Admin',
            'apellidos'=> 'Leadchain',
            'email'    => LoginData::LOGIN_ADMIN['email'],
            'password' => Hash::make(LoginData::LOGIN_ADMIN['password']),
            'rol'      => 'admin'
        ]);

        $response = $this->postJson('/api/auth/login', LoginData::LOGIN_ADMIN);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login exitoso')
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonPath('user.email', LoginData::LOGIN_ADMIN['email'])
            ->assertJsonPath('user.rol', 'admin')
            ->assertJsonPath('dashboard', '/admin/dashboard')
            ->assertJsonStructure([
                'access_token',
                'expires_in',
                'user' => ['id', 'nombre', 'apellidos', 'email', 'rol', 'id_zona'],
            ]
        );
    }

    public function test_comercial_can_login_successfully(): void
    {
        User::create([
            'nombre'   => 'Juan',
            'apellidos'=> 'García',
            'email'    => LoginData::LOGIN_COMERCIAL['email'],
            'password' => Hash::make(LoginData::LOGIN_COMERCIAL['password']),
            'rol'      => 'comercial'
        ]);

        $response = $this->postJson('/api/auth/login', LoginData::LOGIN_COMERCIAL);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login exitoso')
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonPath('user.email', LoginData::LOGIN_COMERCIAL['email'])
            ->assertJsonPath('user.rol', 'comercial')
            ->assertJsonPath('dashboard', '/comercial/dashboard')
            ->assertJsonStructure([
                'access_token',
                'expires_in',
                'user' => ['id', 'nombre', 'apellidos', 'email', 'rol', 'id_zona'],
            ]);
    }
}
