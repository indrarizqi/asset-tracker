<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\User;
use App\Services\AssetStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScannerController extends Controller
{
    public function __construct(
        private readonly AssetStatusService $statusService,
    ) {}

    /**
     * Login dari HP.
     */
    public function login(Request $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Login gagal. Cek email/password.'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        if (! in_array($user->role, ['admin', 'super_admin'])) {
            return response()->json(['message' => 'Maaf, Hanya Admin dan Super Admin yang Boleh Masuk!'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Hi ' . $user->name . ', Selamat Datang!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => $user->role,
        ]);
    }

    /**
     * Endpoint saat camera scan QR.
     */
    public function scan(string $tag): JsonResponse
    {
        $asset = Asset::where('asset_tag', $tag)->first();

        if (! $asset) {
            return response()->json(['success' => false, 'message' => 'Aset Tidak Ditemukan!'], 404);
        }

        $actions = [];

        if ($asset->status == 'available') {
            $actions[] = 'check_out';
        } elseif ($asset->status == 'in_use') {
            $actions[] = 'check_in';
        }

        $actions[] = 'report_issue';

        return response()->json([
            'success' => true,
            'data' => $asset,
            'available_actions' => $actions,
        ]);
    }

    /**
     * Eksekusi update status (check-in / check-out / maintenance).
     * Menggunakan AssetStatusService untuk menghilangkan duplikasi dengan web controller.
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'asset_tag' => 'required|exists:assets,asset_tag',
            'action' => 'required|in:check_in,check_out,maintenance',
            'verified_by_scan' => 'required|boolean',
            'borrower_name' => 'nullable|string|max:255',
            'due_at' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (! $request->boolean('verified_by_scan')) {
            return response()->json(['message' => 'Wajib Scan QR Code Di Lokasi!'], 403);
        }

        /** @var \App\Models\User $user */
        $user = $request->user();

        if (! in_array($user->role, ['admin', 'super_admin'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $asset = Asset::where('asset_tag', $request->asset_tag)->firstOrFail();

        $message = $this->statusService->processStatusUpdate(
            asset: $asset,
            action: $request->action,
            user: $user,
            borrowerName: $request->borrower_name,
            dueAt: $request->due_at,
            notes: $request->notes,
        );

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $asset->fresh(),
        ]);
    }

    /**
     * Logout mobile: revoke token aktif.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['success' => true, 'message' => 'Logout berhasil.']);
    }
}