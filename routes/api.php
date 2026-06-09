<?php

use App\Http\Controllers\Api\V1\WhoisController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/whois')->group(function (): void {
    Route::post('bulk', [WhoisController::class, 'bulk'])
        ->middleware('throttle:whois-bulk');

    Route::get('{domain}', [WhoisController::class, 'single'])
        ->middleware('throttle:whois')
        ->where('domain', '[^/]+(?:\.[^/]+)*');
});
