<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ScannerController;
use Illuminate\Support\Facades\Route;

// Public Route (Login)
Route::post('/login', [ScannerController::class, 'login']);

// Protected Routes (Harus pakai Token/Login dulu)
Route::middleware('auth:sanctum')->group(function () {
    
    // Scan QR (Mendapatkan info aset)
    Route::get('/scan/{tag}', [ScannerController::class, 'scan']);
    
    // Update Status (Check in/out)
    Route::post('/asset/action', [ScannerController::class, 'updateStatus']);
    
    // Cek User sedang login siapa
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});