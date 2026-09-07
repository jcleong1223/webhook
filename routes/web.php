<?php

use App\Http\Controllers\Admin\InternalApiClientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('api-clients', [InternalApiClientController::class, 'index'])
            ->name('api-clients.index');
    });
