<?php

use App\Http\Controllers\Api\V1\WhoisController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('whois/{domain}/raw', [WhoisController::class, 'raw'])
        ->where('domain', '[^/]+(?:\.[^/]+)*');

    Route::get('whois/{domain}', [WhoisController::class, 'show'])
        ->where('domain', '[^/]+(?:\.[^/]+)*');
});
