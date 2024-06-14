<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FileTest extends TestCase
{
    public function testGetFiles()
    {
        $response = $this->get('/api/fms/v1/files');

        $response->assertStatus(200);
    }

    public function testCreateFile()
    {
        $response = $this->postJson('/api/fms/v1/files/create', [
            'name' => 'test.txt',
            'content' => 'Hello World!',
            'license_id' => 1,
            'user_id' => 1,
            'mime_type' => 'text/plain',
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'uuid',
            'name',
            'path',
            'mime_type',
            'size',
            'user_id',
            'license_id',
            'created_at',
            'updated_at',
        ]);
    }

    public function testStoreFile()
    {
        $response = $this->postJson('/api/fms/v1/files/store', [
            'name' => 'test.txt',
            'file' => UploadedFile::fake()->create('test.txt', 100),
            'license_id' => 1,
            'user_id' => 1,
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'uuid',
            'name',
            'path',
            'mime_type',
            'size',
            'user_id',
            'license_id',
            'created_at',
            'updated_at',
        ]);
    }

    public function testShowFile()
    {
        $file = $this->postJson('/api/fms/v1/files/store', [
            'name' => 'test.txt',
            'file' => UploadedFile::fake()->create('test.txt', 100),
            'license_id' => 1,
            'user_id' => 1,
        ]);
        $response = $this->get('/api/fms/v1/files/show/' . $file->json('uuid'));

        $response->assertStatus(200)
        ->assertJsonStructure([
            'uuid',
            'name',
            'path',
            'mime_type',
            'size',
            'user_id',
            'license_id',
            'created_at',
            'updated_at',
        ]);

    }

    public function testDownloadFile()
    {
        $file = $this->postJson('/api/fms/v1/files/store', [
            'name' => 'test.txt',
            'file' => UploadedFile::fake()->create('test.txt', 100),
            'license_id' => 1,
            'user_id' => 1,
        ]);
        $response = $this->get('/api/fms/v1/files/download/' . $file->json('uuid'));

        $response->assertStatus(200);
    }

    public function testDeleteFile()
    {
        $file = $this->postJson('/api/fms/v1/files/store', [
            'name' => 'test.txt',
            'file' => UploadedFile::fake()->create('test.txt', 100),
            'license_id' => 1,
            'user_id' => 1,
        ]);
        $response = $this->delete('/api/fms/v1/files/delete/' . $file->json('uuid'));

        $response->assertStatus(204);
    }

    public function testUpdateFile()
    {
        $file = $this->postJson('/api/fms/v1/files/store', [
            'name' => 'test.txt',
            'file' => UploadedFile::fake()->create('test.txt', 100),
            'license_id' => 1,
            'user_id' => 1,
        ]);
        $response = $this->postJson('/api/fms/v1/files/update/' . $file->json('uuid'), [
            'name' => 'test2.txt',
            'file' => UploadedFile::fake()->create('test2.txt', 100),
            'license_id' => 1,
            'user_id' => 1,
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'uuid',
            'name',
            'path',
            'mime_type',
            'size',
            'user_id',
            'license_id',
            'created_at',
            'updated_at',
        ]);
    }
}
