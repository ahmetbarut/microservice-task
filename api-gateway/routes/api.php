<?php

use App\Http\Controllers\FileManagementController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\SecurityController;
use App\Http\Middleware\CheckTokenMiddleware;
use App\Http\Middleware\QuotaCheckMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('security/v1')->group(function () {
    Route::prefix('users')
        ->group(function () {
            Route::post('/', [SecurityController::class, 'store']);
            Route::post('/login', [SecurityController::class, 'login']);
        });
});

Route::middleware(CheckTokenMiddleware::class)
    ->group(function () {
        Route::prefix('security/v1')
            ->group(function () {
                Route::get('/users', [SecurityController::class, 'index']);
                Route::put('/users/{user}', [SecurityController::class, 'update']);
                Route::delete('/users/{user}', [SecurityController::class, 'destroy']);
            });
        Route::prefix('license/v1')
            ->group(function () {
                Route::get('/me', [LicenseController::class, 'index']);
            });
        Route::prefix('fms/v1/files')->group(function () {
            Route::post('create', [FileManagementController::class, 'createFile'])
                ->middleware(QuotaCheckMiddleware::class);
            Route::post('store', [FileManagementController::class, 'storeFile'])
                ->middleware(QuotaCheckMiddleware::class);

            Route::get('show/{file}', [FileManagementController::class, 'showFile']);
            Route::get('download/{file}', [FileManagementController::class, 'downloadFile']);
            Route::delete('delete/{file}', [FileManagementController::class, 'deleteFile']);
            Route::get('quota', [FileManagementController::class, 'quota']);
        });
    });
