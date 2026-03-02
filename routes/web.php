<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetStatusController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('/', [AuthenticatedSessionController::class, 'store'])->middleware('guest')->name('login.post');

// Dashboard
Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

// --- GRUP ROUTE YANG WAJIB LOGIN ---
Route::middleware('auth')->group(function () {

    // === 1. FITUR OPERASIONAL ASET (Admin & Super Admin) ===
    Route::middleware(['role:super_admin,admin'])->group(function () {
        // CRUD Aset
        Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
        Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets/store', [AssetController::class, 'store'])->name('assets.store');
        Route::get('/assets/{id}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/assets/{id}', [AssetController::class, 'update'])->name('assets.update');
        Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->name('assets.destroy');

        // Status Update (Check-in/out)
        Route::post('/assets/update-status', [AssetStatusController::class, 'updateFromWeb'])->name('assets.update-status');
        Route::get('/assets/history', [AssetStatusController::class, 'history'])->name('assets.history');

        // Report & Print
        Route::get('/assets/print', [ReportController::class, 'printPreview'])->name('assets.print');
        Route::get('/assets/download-pdf', [ReportController::class, 'downloadPdf'])->name('assets.pdf');
        Route::get('/assets/export', [ReportController::class, 'exportReport'])->name('assets.export');
        Route::get('/report/assets', [ReportController::class, 'exportReport'])->name('report.assets');

        // API untuk Select All
        Route::get('/api/assets/all-ids', [AssetController::class, 'getAllAssetIds'])->name('assets.all-ids');
    });

    // === 2. FITUR SISTEM CORE (KHUSUS Super Admin) ===
    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    });

});

// Mobile Scanner
Route::get('/mobile', function () {
    return view('mobile_scanner');
});

require __DIR__.'/auth.php';