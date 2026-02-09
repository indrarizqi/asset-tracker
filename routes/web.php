<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Halaman Depan (Login)
Route::get('/', function () {
    return view('auth.login');
});

// Dashboard
Route::get('/dashboard', [AssetController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// --- GRUP ROUTE YANG WAJIB LOGIN ---
Route::middleware('auth')->group(function () {

    // 1. Rute Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. Fitur Asset Tracking (Operator & Admin)
    Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/assets/store', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/print', [AssetController::class, 'printPreview'])->name('assets.print');
    Route::get('/assets/download-pdf', [AssetController::class, 'downloadPdf'])->name('assets.pdf');
    Route::get('/assets/{id}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{id}', [AssetController::class, 'update'])->name('assets.update');

    // 3. Area Khusus Super Admin
    Route::middleware(['role:super_admin'])->group(function () {
        Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->name('assets.destroy');
        Route::resource('users', UserController::class);
        Route::get('/report/assets', [AssetController::class, 'exportReport'])->name('report.assets');
    });
});

// Mobile Scanner
Route::get('/mobile', function () {
    return view('mobile_scanner');
});

require __DIR__.'/auth.php';