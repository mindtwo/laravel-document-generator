<?php

use Illuminate\Support\Facades\Route;
use mindtwo\DocumentGenerator\Http\Controllers\DocumentController;

/**
 * Routes for documents generator package
 */
Route::name('documents.')->prefix('documents')->middleware(
    config('documents.security.middleware', 'web')
)->group(function () {
    Route::get('/tmp/{fileName}', [DocumentController::class, 'getTmp'])->name('tmp');
    Route::get('/download/{documentId}', [DocumentController::class, 'download'])->name('download');

    Route::prefix('/{layoutIdentifier}')->group(function () {
        Route::get('/', [DocumentController::class, 'show'])->name('show');
        Route::post('/', [DocumentController::class, 'update'])->name('update');
    });
});
