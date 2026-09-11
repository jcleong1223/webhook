<?php

use App\Http\Controllers\Admin\InternalApiClientController;
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
                return redirect()->route('admin.api-clients.index');
            }

            if ($user->hasRole('support-administrator')) {
                return redirect('/' . trim(config('horizon.path', 'horizon'), '/'));
            }

            abort(403, 'No admin pages are available for your role yet.');
        })->name('home');

        Route::middleware('permission:api-clients.manage')->prefix('api-clients')->as('api-clients.')->group(function () {
            Route::get('/', [InternalApiClientController::class, 'index'])->name('index');
            Route::get('datatable', [InternalApiClientController::class, 'apiClientsDatatable'])->name('datatable');
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
