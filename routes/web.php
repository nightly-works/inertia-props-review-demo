<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiteJobController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/dashboard'));
Route::view('/login', 'welcome')->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class);
    Route::get('/clients/{client}', [ClientController::class, 'show']);
    Route::get('/jobs/{job}/edit', [SiteJobController::class, 'edit']);
    Route::put('/jobs/{job}', [SiteJobController::class, 'update']);
});
