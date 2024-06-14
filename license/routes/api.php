<?php

use App\Http\Controllers\LicenseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/v1/license', [LicenseController::class, 'index']);
