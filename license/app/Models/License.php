<?php

namespace App\Models;

use App\Enums\LicenseStatus;
use App\Enums\LicenseType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'daily_file_limit',
        'max_storage',
        'quota',
        'user_id',
        'status',
        'license_type',
        'expires_at',
        'revoked_at',
    ];

    protected $casts = [
        'status' => LicenseStatus::class,
        'license_type' => LicenseType::class,
        'expires_at' => 'datetime',
        'is_default' => 'boolean',
    ];

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
