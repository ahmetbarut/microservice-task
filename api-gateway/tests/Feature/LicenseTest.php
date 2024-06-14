<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LicenseTest extends TestCase
{
    public function testGetLicense(): void
    {
        $response = $this->postJson('/api/security/v1/users', [
            'name' => 'John Doe',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]);

        $this
            ->withToken($response->json('data.access_token'))
            ->getJson('/api/license/v1/me')
            ->assertStatus(200);
    }
}
