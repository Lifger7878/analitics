<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AnalyticsController::class, 'index'])->name('analytics.index');
