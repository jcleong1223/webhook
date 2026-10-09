<?php

use App\Http\Controllers\Admin\InternalApiClientController;
use App\Http\Controllers\Admin\WebhookDeliveryAttemptController;
use App\Http\Controllers\Admin\WebhookDeliveryController;
use App\Http\Controllers\Admin\WebhookEndpointHealthController;
use App\Http\Controllers\Admin\WebhookEndpointSnapshotController;
use App\Http\Controllers\Admin\WebhookEventController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/horizon/dashboard');
    }

    return redirect('/login');
});

Route::middleware(['auth', 'role:super-administrator|support-administrator|tenant-administrator'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', function () {
            $user = auth()->user();

            if ($user->can('api-clients.manage')) {
                return redirect()->route('admin.api-client.index');
            }

            if ($user->hasRole('support-administrator')) {
                return redirect('/' . trim(config('horizon.path', 'horizon'), '/'));
            }

            abort(403, 'No admin pages are available for your role yet.');
        })->name('home');

        Route::middleware('permission:api-clients.manage')->prefix('api-client')->as('api-client.')->group(function () {
            Route::get('/', [InternalApiClientController::class, 'index'])->name('index');
            Route::get('create', [InternalApiClientController::class, 'create'])->name('create');
            Route::post('/', [InternalApiClientController::class, 'store'])->name('store');
            Route::get('datatable', [InternalApiClientController::class, 'apiClientsDatatable'])->name('datatable');
        });

        Route::middleware('permission:api-clients.manage')->prefix('webhook-event')->as('webhook-event.')->group(function () {
            Route::get('/', [WebhookEventController::class, 'index'])->name('index');
            Route::get('datatable', [WebhookEventController::class, 'webhookEventsDatatable'])->name('datatable');
            Route::get('/{id}', [WebhookEventController::class, 'show'])->name('show');
        });

        Route::middleware('permission:api-clients.manage')->prefix('webhook-delivery')->as('webhook-delivery.')->group(function () {
            Route::get('/', [WebhookDeliveryController::class, 'index'])->name('index');
            Route::get('datatable', [WebhookDeliveryController::class, 'webhookDeliveriesDatatable'])->name('datatable');
            Route::get('/{id}', [WebhookDeliveryController::class, 'show'])->name('show');
        });

        Route::middleware('permission:api-clients.manage')->prefix('webhook-endpoint-snapshot')->as('webhook-endpoint-snapshot.')->group(function () {
            Route::get('datatable', [WebhookEndpointSnapshotController::class, 'endpointSnapshotDatatable'])->name('datatable');
            Route::get('/{id}', [WebhookEndpointSnapshotController::class, 'show'])->name('show');
        });

        Route::middleware('permission:api-clients.manage')->prefix('endpoint-health')->as('endpoint-health.')->group(function () {
            Route::get('/', [WebhookEndpointHealthController::class, 'index'])->name('index');
            Route::get('datatable', [WebhookEndpointHealthController::class, 'endpointHealthDatatable'])->name('datatable');
            Route::get('/{healthId}', [WebhookEndpointHealthController::class, 'show'])->name('show');
        });

        Route::middleware('permission:api-clients.manage')->prefix('delivery-attempt')->as('delivery-attempt.')->group(function () {
            Route::get('/', [WebhookDeliveryAttemptController::class, 'index'])->name('index');
            Route::get('datatable', [WebhookDeliveryAttemptController::class, 'deliveryAttemptDatatable'])->name('datatable');
        });


        // Super-admin only: internal API client credentials.


        /*
         * Future pages — protect each with its permission, and scope lists with ->visibleTo($user):
         * Route::get('webhook-events', ...)->middleware('permission:webhook-events.view')->name('webhook-events.index');
         * Route::get('deliveries', ...)->middleware('permission:deliveries.view')->name('deliveries.index');
         * Route::post('deliveries/{delivery}/redeliver', ...)->middleware('permission:events.redeliver')->name('deliveries.redeliver');
         * Route::post('deliveries/{delivery}/cancel', ...)->middleware('permission:deliveries.cancel')->name('deliveries.cancel');
         * Route::get('endpoint-health', ...)->middleware('permission:endpoint-health.view')->name('endpoint-health.index');
         * Route::get('audit-logs', ...)->middleware('permission:audit-logs.view')->name('audit-logs.index');
         */
    });