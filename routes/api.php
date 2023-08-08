<?php

use Illuminate\Support\Facades\Route;
use mindtwo\DocumentGenerator\Http\Controllers\DocumentController;

/**
 * Routes for documents generator package
 */
Route::name('documents.')->prefix('documents')->group(function () {
    Route::get('/download/{documentId}', [DocumentController::class, 'download'])->name('download')->middleware(
        config('documents.security.download_middleware', 'web')
    );

    // Admin routes
    Route::middleware(
        config('documents.security.admin_middleware', 'web')
    )->group(function () {
        Route::get('/tmp/{fileName}', [DocumentController::class, 'getTmp'])->name('tmp');

        Route::prefix('/{layoutIdentifier}')->group(function () {
            Route::get('/', [DocumentController::class, 'show'])->name('show');
            Route::post('/', [DocumentController::class, 'update'])->name('update');
        });
    });
});
