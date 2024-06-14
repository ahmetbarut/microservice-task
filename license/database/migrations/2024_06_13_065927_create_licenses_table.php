<?php

use App\Enums\LicenseStatus;
use App\Enums\LicenseType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('DEMO');
            $table->unsignedBigInteger('daily_file_limit')->default(5);
            $table->unsignedBigInteger('max_storage')->default(100); // in MB
            $table->unsignedBigInteger('quota')->default(1024 * 2); // in MB (2GB)
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default(LicenseStatus::Active->value);
            $table->string('license_type')->default(LicenseType::Trial->value);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
