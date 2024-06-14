<?php

namespace Tests\Feature;

use App\Models\License;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LicenseTest extends TestCase
{
    public function testGetLicense()
    {
        $license = $this->createLicense();

        $response = $this->getJson('/api/v1/license?user_id=1');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'quota',
                'license_type',
                'status',
                'user_id',
                'created_at',
                'updated_at',
            ]
        ]);
    }

    public function createLicense()
    {
        License::query()->create([
            'name' => 'DEMO',
            'daily_file_limit' => 5,
            'max_storage' => 100,
            'quota' => 1024 * 2,
            'status' => 'Active',
            'license_type' => 'Trial',
            'user_id' => 1,
        ]);
    }
}
