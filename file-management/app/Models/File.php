<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'path',
        'mime_type',
        'size',
        'user_id',
        'license_id',
        'uuid',
    ];

    public static function booted()
    {
        static::created(function ($file) {
            // publish rabbitmq event
        });

        static::deleted(function ($file) {
            // publish rabbitmq event
        });
    }

    public function getKeyName()
    {
        return 'uuid';
    }
}
