<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Depends;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testUserRegister()
    {
        $email = fake()->unique()->safeEmail();
        $response = $this->postJson('/api/security/v1/users', [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'password',
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'access_token'
            ],
        ]);
    }

    public function testUserLogin()
    {
        $email = fake()->unique()->safeEmail();
        $response = $this->postJson('/api/security/v1/users', [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/security/v1/users/login', [
            'email' => $email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'access_token'
            ],
        ]);

        return $response->json('data.access_token');
    }

    #[Depends('testUserLogin')]
    public function testUserIndex(string $token)
    {
        $response = $this
        ->withToken($token)
        ->getJson('/api/security/v1/users');

        $response->assertStatus(200);
    }
}
