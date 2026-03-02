<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('/', [AuthenticatedSessionController::class, 'store'])->middleware('guest')->name('login.post');

// Dashboard
Route::get('/dashboard', [AssetController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

// --- GRUP ROUTE YANG WAJIB LOGIN ---
Route::middleware('auth')->group(function () {

    // === 1. FITUR OPERASIONAL ASET (Bisa diakses semua role) ===
    Route::middleware(['role:super_admin,admin'])->group(function () {
        Route::get('/assets', [AssetController::class, 'index'])->name('assets.index'); // Kelola aset
        Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets/store', [AssetController::class, 'store'])->name('assets.store');
        Route::post('/assets/update-status', [AssetController::class, 'updateStatusFromWeb'])->name('assets.update-status');
        Route::get('/assets/print', [AssetController::class, 'printPreview'])->name('assets.print');
        Route::get('/assets/download-pdf', [AssetController::class, 'downloadPdf'])->name('assets.pdf');
        Route::get('/assets/{id}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/assets/{id}', [AssetController::class, 'update'])->name('assets.update');
        Route::get('/assets/export', [App\Http\Controllers\AssetController::class, 'export'])->name('assets.export');
        Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->name('assets.destroy'); 
        Route::get('/report/assets', [AssetController::class, 'exportReport'])->name('report.assets'); 
        
        // API untuk Select All
        Route::get('/api/assets/all-ids', [AssetController::class, 'getAllAssetIds'])->name('assets.all-ids');
    });

    // === 2. FITUR SISTEM CORE (KHUSUS Super Admin) ===
    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/approvals', [AssetController::class, 'approvalQueue'])->name('approvals.index');
        Route::post('/approvals/{id}/approve', [AssetController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{id}/reject', [AssetController::class, 'reject'])->name('approvals.reject');
    });

});

// Mobile Scanner
Route::get('/mobile', function () {
    return view('mobile_scanner');
});

require __DIR__.'/auth.php';