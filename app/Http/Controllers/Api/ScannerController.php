<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScannerController extends Controller
{
    // 1. Login dari HP
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Login gagal. Cek email/password.'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // --- VALIDASI ROLE (HANYA ADMIN & SUPER ADMIN) ---
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            return response()->json(['message' => 'Maaf, Hanya Admin dan Super Admin yang Boleh Masuk!'], 403);
        }
        // ----------------------------------------

        // Buat token (kunci akses) untuk HP
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Hi ' . $user->name . ', Selamat Datang!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => $user->role,
        ]);
    }

    // 2. Endpoint saat Camera Scan QR
    public function scan($tag)
    {
        // Cari aset berdasarkan ID Tag (misal: M-24-001)
        $asset = Asset::where('asset_tag', $tag)->first();

        if (!$asset) {
            return response()->json(['success' => false, 'message' => 'Aset Tidak Ditemukan!'], 404);
        }

        // Tentukan Menu apa yang boleh muncul di HP berdasarkan Kategori & Status
        $actions = [];
        
        if ($asset->status == 'available') {
            $actions[] = 'check_out'; // Menu "Pinjam / Pakai"
        } elseif ($asset->status == 'in_use') {
            $actions[] = 'check_in'; // Menu "Kembalikan"
        }
        
        $actions[] = 'report_issue'; 

        return response()->json([
            'success' => true,
            'data' => $asset,
            'available_actions' => $actions
        ]);
    }

    // 3. Eksekusi Update Status (Check-in / Check-out)
    public function updateStatus(Request $request)
    {
        $request->validate([
            'asset_tag' => 'required|exists:assets,asset_tag',
            'action' => 'required|in:check_in,check_out,maintenance', // check_in, check_out, maintenance
            'verified_by_scan' => 'required|boolean' // KEAMANAN: Wajib true
        ]);

        if (! $request->boolean('verified_by_scan')) {
            return response()->json(['message' => 'Wajib Scan QR Code Di Lokasi!'], 403);
        }

        $user = $request->user();

        if (! in_array($user->role, ['admin', 'super_admin'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $asset = Asset::where('asset_tag', $request->asset_tag)->firstOrFail();
        $oldData = $asset->toArray();

        // Logika Ganti Status
        if ($request->action == 'check_out') {
            $newStatus = 'in_use';
            $message = 'Aset berhasil dipinjam/digunakan.';
        } elseif ($request->action == 'check_in') {
            $newStatus = 'available';
            $message = 'Aset berhasil dikembalikan.';
        } elseif ($request->action == 'maintenance') {
            $newStatus = 'maintenance';
            $message = 'Aset masuk maintenance.';
        } else {
            return response()->json(['message' => 'Aksi Tidak Diketahui'], 400);
        }

        DB::transaction(function () use ($asset, $newStatus, $request, $oldData, $user): void {
            $asset->update(['status' => $newStatus]);

            AssetLog::create([
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'action' => $request->action,
                'old_data' => $oldData,
                'new_data' => $asset->fresh()->toArray(),
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $asset->fresh(),
        ]);
    }

    // 4. Logout mobile: revoke token aktif
    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['success' => true, 'message' => 'Logout berhasil.']);
    }
}