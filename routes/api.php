<?php

use App\Http\Controllers\Internal\WebhookEventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Internal API Routes
|--------------------------------------------------------------------------
|
| Authenticated machine-to-machine routes called by the SGDataPOS legacy
| backoffice. Every request must carry the X-SGDP-* headers and a valid
| HMAC-SHA256 signature verified by the AuthenticateInternalClient
| middleware (spec §9.2).
|
*/

Route::middleware(['api', 'client.auth'])->prefix('internal')->name('internal.')->group(function () {
    Route::post('v1/webhook-events', [WebhookEventController::class, 'store'])
        ->name('webhook-events.store');


    Route::post('v1/testing', [WebhookEventController::class, 'testing'])
        ->name('testing');
});
