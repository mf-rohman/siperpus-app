<?php

use App\Http\Controllers\BeritaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/registrasi',                  [RegistrasiController::class, 'index'])->name('registrasi.index');
    Route::get('/api/mahasiswa/search',        [RegistrasiController::class, 'search'])->name('registrasi.search');
    Route::post('/api/mahasiswa/registrasi',   [RegistrasiController::class, 'toggle'])->name('registrasi.toggle');
    Route::get('/scan',      [ScanController::class,     'index'])->name('scan.index');
    Route::post('/api/scan', [ScanController::class,     'store'])->name('scan.store');
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::get('/api/kunjungan/stats', [DashboardController::class, 'stats'])->name('kunjungan.stats');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/berita',                    [BeritaController::class, 'index'])->name('berita.index');
    Route::get('/api/berita/admin',          [BeritaController::class, 'adminList']);
    Route::post('/api/berita',               [BeritaController::class, 'store']);
    Route::post('/api/berita/{id}',          [BeritaController::class, 'update']);
    Route::delete('/api/berita/{id}',        [BeritaController::class, 'destroy']);
    Route::post('/api/berita/{id}/publish',  [BeritaController::class, 'togglePublish']);
});

Route::get('/api/berita',                [BeritaController::class, 'list']);

require __DIR__.'/auth.php';
