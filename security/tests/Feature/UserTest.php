<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRegister(): void
    {
        $response = $this->postJson('api/security/v1/users', [
            'name' => 'John Doe',
            'email' => fake()->safeEmail(),
            'password' => 'password',
        ]);

        $response

            ->assertStatus(201);
    }

    public function testLogin(): void
    {
        $user = User::factory()->create([
            'password' => 'password'
        ]);

        $response = $this->postJson('api/security/v1/users/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
            ]);
    }

    public function testGetUsers(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('api/security/v1/users');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    public function testDeleteUser(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson('api/security/v1/users/' . $user->id);

        $response
            ->assertStatus(204);
    }

    public function testUpdateUser(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('api/security/v1/users/' . $user->id, [
            'name' => 'Jane Doe',
            
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
            ]);
    }
}
