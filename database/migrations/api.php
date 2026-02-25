<?php

// routes/api.php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

Route::post('/scan', [ScanController::class, 'store']);
Route::get('/kunjungan/stats', [DashboardController::class, 'stats']);
