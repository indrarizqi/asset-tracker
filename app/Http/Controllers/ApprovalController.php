<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    /**
     * Tampilkan halaman antrean approval.
     */
    public function index(): View
    {
        $pendingLogs = AssetLog::with(['user', 'asset'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        $pendingLogs->getCollection()->transform(function ($log) {
            $oldData = $log->old_data ?? [];
            $newData = $log->new_data ?? [];
            $diff = [];

            if ($log->action === 'update') {
                foreach ($newData as $key => $newValue) {
                    $oldValue = $oldData[$key] ?? null;
                    if ((string) $oldValue !== (string) $newValue) {
                        $diff[$key] = [
                            'old' => $oldValue,
                            'new' => $newValue,
                        ];
                    }
                }
            }

            $log->changed_fields = $diff;

            return $log;
        });

        return view('assets.approvals', compact('pendingLogs'));
    }

    /**
     * Setujui permintaan perubahan (approve).
     */
    public function approve(int $id): RedirectResponse
    {
        $log = AssetLog::findOrFail($id);
        $asset = Asset::find($log->asset_id);

        if (! $asset && $log->action !== 'delete') {
            return back()->with('error', 'Aset terkait tidak ditemukan.');
        }

        if ($log->action === 'update') {
            $asset->update($log->new_data ?? []);
        } elseif ($log->action === 'delete') {
            $asset?->delete();
        }

        $log->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Tiket permintaan ' . strtoupper($log->action) . ' berhasil disetujui!');
    }

    /**
     * Tolak permintaan perubahan (reject).
     */
    public function reject(Request $request, int $id): RedirectResponse
    {
        $log = AssetLog::findOrFail($id);

        $log->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_note' => $request->rejection_note ?? 'Ditolak oleh Super Admin tanpa catatan.',
        ]);

        return back()->with('error', 'Tiket permintaan ' . strtoupper($log->action) . ' telah ditolak.');
    }
}
