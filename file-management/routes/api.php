<?php

use App\Http\Controllers\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('fms/v1/files')->group(function () {
    Route::get('/', [FileController::class, 'index']);
    Route::post('create', [FileController::class, 'createFile']);
    Route::post('store', [FileController::class, 'storeFile']);
    Route::get('show/{file}', [FileController::class, 'showFile']);
    Route::get('download/{file}', [FileController::class, 'downloadFile']);
    Route::delete('delete/{file}', [FileController::class, 'deleteFile']);
    Route::get('quota', [FileController::class, 'quota']);
    Route::post('update/{file}', [FileController::class, 'updateFile']);
});
