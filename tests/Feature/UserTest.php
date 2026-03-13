<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use tests\Fixture\LoginData;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        // $tokenLogin = $this->postJson('/api/auth/login', [LoginData::LOGIN_ADMIN])->json('access_token');
        // $response = $this->withHeaders([
        //     'Authorization' => 'Bearer ' . $tokenLogin,
        // ])->getJson('/api/users');

        // $response->assertStatus(200);

    }
}
