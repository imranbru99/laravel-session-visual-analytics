<?php

use Illuminate\Support\Facades\Route;
use BlogCutter\LaravelSessionVisualAnalytics\Controllers\SessionAnalyticsController;

Route::middleware(['web'])->group(function () {
    Route::get('session', [SessionAnalyticsController::class, 'sessions'])->name('session.analytics');
    Route::delete('session/delete-all', [SessionAnalyticsController::class, 'deleteAll'])->name('session.analytics.deleteAll');
});
