<?php

use Illuminate\Support\Facades\Route;
use Imranbru99\SessionVisualAnalytics\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/sessions', [DashboardController::class, 'sessions'])->name('sessions');
Route::get('/devices', [DashboardController::class, 'devices'])->name('devices');
Route::get('/api/overview', [DashboardController::class, 'apiOverview'])->name('api.overview');
