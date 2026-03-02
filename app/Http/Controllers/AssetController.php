<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\AssetAttachment;
use App\Models\AssetLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View|string
    {
        $query = Asset::query()->with('activeTransaction');

        $this->applyAssetFilters($request, $query);

        $assets = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('assets.partials.table', compact('assets'))->render();
        }

        return view('assets.index', compact('assets'));
    }

    public function create(): View
    {
        return view('assets.create');
    }

    public function store(StoreAssetRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $prefix = match ($validated['category']) {
            'mobile' => 'M',
            'semi-mobile' => 'SM',
            'fixed' => 'F',
        };
        $year = date('y');

        $lastAsset = Asset::where('asset_tag', 'LIKE', "$prefix-$year-%")
            ->orderBy('id', 'desc')->first();
        $sequence = $lastAsset ? intval(substr($lastAsset->asset_tag, -3)) + 1 : 1;
        $newTag = sprintf("%s-%s-%03d", $prefix, $year, $sequence);

        $asset = Asset::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'asset_tag' => $newTag,
            'status' => $validated['status'],
            'purchase_date' => $validated['purchase_date'],
            'warranty_expiry_date' => $validated['warranty_expiry_date'] ?? null,
            'condition' => $validated['condition'],
            'price' => $validated['price'] ?? 0,
            'person_in_charge' => $validated['person_in_charge'] ?? null,
            'location' => $validated['location'] ?? null,
            'vendor' => $validated['vendor'] ?? null,
            'serial_number' => $validated['serial_number'] ?? null,
            'description' => $validated['description'],
        ]);

        $this->storeAttachments($request, $asset, Auth::id());

        /** @var \App\Models\User $user */
        $user = Auth::user();

        AssetLog::create([
            'asset_id' => $asset->id,
            'user_id' => $user->id,
            'action' => 'create',
            'new_data' => $asset->toArray(),
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return redirect()->route('assets.index')->with('success', 'Aset Baru Berhasil Ditambah: ' . $newTag);
    }

    public function edit(int $id): View
    {
        $asset = Asset::with('attachments')->findOrFail($id);

        return view('assets.edit', compact('asset'));
    }

    public function update(UpdateAssetRequest $request, int $id): RedirectResponse
    {
        $validated = $request->validated();
        $asset = Asset::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $newData = [
            'name' => $validated['name'],
            'category' => $validated['category'],
            'status' => $validated['status'],
            'description' => $validated['description'],
            'purchase_date' => $validated['purchase_date'],
            'warranty_expiry_date' => $validated['warranty_expiry_date'] ?? null,
            'condition' => $validated['condition'],
            'price' => $validated['price'] ?? 0,
            'person_in_charge' => $validated['person_in_charge'] ?? null,
            'location' => $validated['location'] ?? null,
            'vendor' => $validated['vendor'] ?? null,
            'serial_number' => $validated['serial_number'] ?? null,
        ];

        if ($user->role === 'super_admin') {
            $oldData = $asset->toArray();
            $asset->update($newData);

            AssetLog::create([
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'action' => 'update',
                'old_data' => $oldData,
                'new_data' => $asset->toArray(),
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            $this->storeAttachments($request, $asset, $user->id);

            return redirect()->route('assets.index')->with('success', 'Data Aset Berhasil Diperbarui!');
        }

        // Admin: Masuk antrean log (data asli tidak berubah dulu)
        AssetLog::create([
            'asset_id' => $asset->id,
            'user_id' => $user->id,
            'action' => 'update',
            'old_data' => $asset->toArray(),
            'new_data' => $newData,
            'status' => 'pending',
        ]);

        return redirect()->route('assets.index')->with('success', 'Permintaan update aset telah dikirim dan menunggu persetujuan Super Admin.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $asset = Asset::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role === 'super_admin') {
            AssetLog::create([
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'action' => 'delete',
                'old_data' => $asset->toArray(),
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            $asset->delete();

            return redirect()->route('assets.index')->with('success', 'Aset Berhasil Dihapus.');
        }

        // Admin: Request hapus
        AssetLog::create([
            'asset_id' => $asset->id,
            'user_id' => $user->id,
            'action' => 'delete',
            'old_data' => $asset->toArray(),
            'status' => 'pending',
        ]);

        return redirect()->route('assets.index')->with('success', 'Permintaan hapus aset dikirim ke Super Admin.');
    }

    /**
     * API: Mengambil semua ID aset tanpa pagination.
     */
    public function getAllAssetIds(Request $request): JsonResponse
    {
        $query = Asset::query();

        $this->applyAssetFilters($request, $query);

        $assetIds = $query->pluck('id')->toArray();

        return response()->json([
            'success' => true,
            'total' => count($assetIds),
            'ids' => $assetIds,
        ]);
    }

    private function applyAssetFilters(Request $request, $query): void
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('asset_tag', 'like', '%' . $search . '%')
                    ->orWhere('category', 'like', '%' . $search . '%')
                    ->orWhere('serial_number', 'like', '%' . $search . '%')
                    ->orWhere('vendor', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->date_to);
        }
    }

    private function storeAttachments(Request $request, Asset $asset, ?int $uploadedBy = null): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store('asset-attachments', 'public');

            AssetAttachment::create([
                'asset_id' => $asset->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $uploadedBy,
            ]);
        }
    }
}