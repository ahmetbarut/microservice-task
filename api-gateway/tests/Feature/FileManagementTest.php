<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FileManagementTest extends TestCase
{
    #[Test]
    public function login(): string
    {
        $response = $this->postJson('api/security/v1/users', [
            'email' => fake()->safeEmail(),
            'name' => fake()->name(),
            'password' => 'password',
        ]);

        return $response->json('data.access_token');
    }

    #[Depends('login')]
    public function testCreate(string $token): void
    {
        $this
            ->withToken($token)
            ->postJson('/api/fms/v1/files/create', [
                'name' => fake()->name(),
                'user_id' => 1,
                'license_id' => 1,
                'content' => fake()->text(),
                'mime_type' => 'txt'
            ])
            ->assertStatus(201);
    }

    #[Depends('login')]
    public function testStore(string $token): void
    {
        $this
            ->withToken($token)
            ->json('POST', '/api/fms/v1/files/store', [
                'name' => fake()->name(),
                'user_id' => 1,
                'license_id' => 1,
                'file' => UploadedFile::fake()->create('file.txt', 100),
                'mime_type' => 'txt'
            ])
            ->assertStatus(201)
            ;
    }

    #[Depends('login')]
    public function testShow(string $token): void
    {
        $file = $this->createFile($token);

        $this
            ->withToken($token)
            ->get('/api/fms/v1/files/show/' . $file->json('uuid'))
            ->assertOk();
    }

    #[Depends('login')]
    public function testDownload(string $token): void
    {
        $file = $this->createFile($token);

        $this
            ->withToken($token)
            ->get('/api/fms/v1/files/download/' . $file->json('uuid'))
            ->assertOk();
    }

    #[Depends('login')]
    public function createFile(string $token)
    {
        return $this
        ->withToken($token)
        ->postJson('/api/fms/v1/files/create', [
            'name' => fake()->name(),
            'user_id' => 1,
            'license_id' => 1,
            'content' => fake()->text(),
            'mime_type' => 'txt'
        ]);
    }

    public function testDelete(string $token): void
    {
        $file = $this->createFile($token);

        $this
            ->withToken($token)
            ->delete('/api/fms/v1/files/delete/' . $file->json('uuid'))
            ->assertOk();
    }
}
