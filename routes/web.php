<?php

use App\Http\Controllers\Admin\InternalApiClientController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\UserController;
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
            if ($user->hasRole('support-administrator')) {
                return redirect('/' . trim(config('horizon.path', 'horizon'), '/'));
            }

            abort(403, 'No admin pages are available for your role yet.');
        })->name('home');

        Route::prefix('api-client')->as('api-client.')->group(function () {
            Route::middleware('permission:api-client.view')->group(function () {
                Route::get('/', [InternalApiClientController::class, 'index'])->name('index');
                Route::get('datatable', [InternalApiClientController::class, 'apiClientsDatatable'])->name('datatable');
            });
            Route::middleware('permission:api-client.create')->group(function () {
                Route::get('create', [InternalApiClientController::class, 'create'])->name('create');
                Route::post('/', [InternalApiClientController::class, 'store'])->name('store');
            });

            Route::middleware('permission:api-client.update')->group(function () {
                Route::get('/{id}', [InternalApiClientController::class, 'edit'])->whereNumber('id')->name('edit');
                Route::post('/{id}', [InternalApiClientController::class, 'update'])->whereNumber('id')->name('update');
            });

            Route::delete('/{id}', [InternalApiClientController::class, 'destroy'])
                    ->whereNumber('id')->name('destroy')->middleware('permission:api-client.delete');
        });

        Route::middleware('permission:webhook-event.view')->prefix('webhook-event')->as('webhook-event.')->group(function () {
            Route::get('/', [WebhookEventController::class, 'index'])->name('index');
            Route::get('datatable', [WebhookEventController::class, 'webhookEventsDatatable'])->name('datatable');
            Route::get('/{id}', [WebhookEventController::class, 'show'])->name('show');
        });

        Route::prefix('webhook-delivery')->as('webhook-delivery.')->group(function () {
            Route::middleware('permission:webhook-delivery.view')->group(function () {
                Route::get('/', [WebhookDeliveryController::class, 'index'])->name('index');
                Route::get('datatable', [WebhookDeliveryController::class, 'webhookDeliveriesDatatable'])->name('datatable');
                Route::get('/{id}', [WebhookDeliveryController::class, 'show'])->name('show');
            });

            Route::post('/{id}/redeliver', [WebhookDeliveryController::class, 'redeliver'])->name('redeliver')->middleware('permission:webhook-delivery.redeliver');
            Route::post('/{id}/cancel', [WebhookDeliveryController::class, 'cancel'])->name('cancel')->middleware('permission:webhook-delivery.cancel');
        });

        Route::middleware('permission:endpoint-health.view')->prefix('webhook-endpoint-snapshot')->as('webhook-endpoint-snapshot.')->group(function () {
            Route::get('datatable', [WebhookEndpointSnapshotController::class, 'endpointSnapshotDatatable'])->name('datatable');
            Route::get('/{id}', [WebhookEndpointSnapshotController::class, 'show'])->name('show');
        });

        Route::middleware('permission:endpoint-health.view')->prefix('endpoint-health')->as('endpoint-health.')->group(function () {
            Route::get('/', [WebhookEndpointHealthController::class, 'index'])->name('index');
            Route::get('datatable', [WebhookEndpointHealthController::class, 'endpointHealthDatatable'])->name('datatable');
            Route::get('/{healthId}', [WebhookEndpointHealthController::class, 'show'])->name('show');
        });

        Route::middleware('permission:delivery-attempt.view')->prefix('delivery-attempt')->as('delivery-attempt.')->group(function () {
            Route::get('/', [WebhookDeliveryAttemptController::class, 'index'])->name('index');
            Route::get('datatable', [WebhookDeliveryAttemptController::class, 'deliveryAttemptDatatable'])->name('datatable');
            Route::get('/{id}', [WebhookDeliveryAttemptController::class, 'show'])->name('show');
        });

        Route::prefix('user')->as('user.')->group(function () {
            Route::middleware('permission:user.view')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('/datatable', [UserController::class, 'usersDatatable'])->name('datatable');
            });
            Route::middleware('permission:user.create')->group(function () {
                Route::get('/create', [UserController::class, 'create'])->name('create');
                Route::post('/', [UserController::class, 'store'])->name('store');
            });
            Route::middleware('permission:user.update')->group(function () {
                Route::get('/{id}', [UserController::class, 'edit'])->whereNumber('id')->name('edit');
                Route::post('/{id}', [UserController::class, 'update'])->whereNumber('id')->name('update');
            });
        });

        Route::prefix('role-permission')->as('role-permission.')->group(function () {
            Route::get('/', [RolePermissionController::class, 'index'])->name('index');
            Route::get('/permission-datatable', [RolePermissionController::class, 'permissionDatatable'])->name('permission-datatable')->middleware('permission:permission.view');
            Route::middleware('permission:role.create')->group(function () {
                Route::get('/create-role', [RolePermissionController::class, 'createRole'])->name('create-role');
                Route::post('/store-role', [RolePermissionController::class, 'storeRole'])->name('store-role');
            });
            Route::middleware('permission:role.update')->group(function () {
                Route::get('/role/{id}/edit', [RolePermissionController::class, 'editRole'])
                    ->whereNumber('id')->name('edit-role');
                Route::put('/role/{id}', [RolePermissionController::class, 'updateRole'])
                    ->whereNumber('id')->name('update-role');
            });
            Route::middleware('permission:permission.create')->group(function () {
                Route::post('/store-permission', [RolePermissionController::class, 'storePermission'])->name('store-permission');
            });
            Route::get('/role/{id}', [RolePermissionController::class, 'showRole'])->name('show-role')->middleware('permission:role.view');
            Route::delete('/destroy-permission/{id}', [RolePermissionController::class, 'destroyPermission'])
                ->middleware('permission:permission.delete')->whereNumber('id')->name('delete-permission');
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