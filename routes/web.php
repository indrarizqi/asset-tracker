<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/', function () {
    return view('auth.login');
});

// Dashboard
Route::get('/dashboard', [AssetController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// --- GRUP ROUTE YANG WAJIB LOGIN ---
Route::middleware('auth')->group(function () {

    // Fitur Asset Tracking (Semua Role)
    Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/assets/store', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/print', [AssetController::class, 'printPreview'])->name('assets.print');
    Route::get('/assets/download-pdf', [AssetController::class, 'downloadPdf'])->name('assets.pdf');
    Route::get('/assets/{id}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{id}', [AssetController::class, 'update'])->name('assets.update');
    
    // API: Get all asset IDs (untuk select all across pages)
    Route::get('/api/assets/all-ids', [AssetController::class, 'getAllAssetIds'])->name('assets.all-ids');

    // Khusus Super Admin
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