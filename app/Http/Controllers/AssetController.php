<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAttachment;
use App\Models\AssetLog;
use App\Models\AssetTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf; // Import facade PDF

class AssetController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        // 1. Data Utama
        $totalAssets = Asset::count();
        $pendingCount = AssetLog::where('status', 'pending')->count();
        
        // 2. Data Status Aset
        $statusAvailable = Asset::where('status', 'available')->count();
        $statusInUse = Asset::where('status', 'in_use')->count();
        $statusMaintenance = Asset::where('status', 'maintenance')->count();
        $statusBroken = Asset::where('status', 'broken')->count();

        // 3. Data Kategori Aset
        $catMobile = Asset::where('category', 'mobile')->count();
        $catSemiMobile = Asset::where('category', 'semi-mobile')->count();
        $catFixed = Asset::where('category', 'fixed')->count();

        // 4. Data Kondisi Aset (Dihitung Eksplisit agar UI Fixed)
        $condBaik = Asset::where('condition', 'like', '%baik%')->count();
        $condRusakTotal = Asset::where(function($q) {
            $q->where('condition', 'like', '%total%')
              ->orWhere('condition', 'like', '%berat%');
        })->count();
        // Rusak biasa = Total yang mengandung kata 'rusak' tapi BUKAN rusak total/berat
        $condRusak = Asset::where('condition', 'like', '%rusak%')
                          ->where('condition', 'not like', '%total%')
                          ->where('condition', 'not like', '%berat%')->count();

        // 5. Data Activity History
        $logs = AssetLog::with(['user', 'asset'])->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalAssets', 'pendingCount', 
            'statusAvailable', 'statusInUse', 'statusMaintenance', 'statusBroken',
            'catMobile', 'catSemiMobile', 'catFixed',
            'condBaik', 'condRusak', 'condRusakTotal', 'logs' 
        ));
    }
    
    public function create()
    {
        return view('assets.create');
    }

    // Simpan Data & Log (CREATE) - Tanpa Approval
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required|in:mobile,semi-mobile,fixed',
            'description' => 'required',
            'status' => 'required|in:in_use,maintenance,broken,available',
            'purchase_date' => 'required',
            'condition' => 'required',
            'location' => 'nullable|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'warranty_expiry_date' => 'nullable|date',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        $prefix = match($request->category) {
            'mobile' => 'M',
            'semi-mobile' => 'SM',
            'fixed' => 'F',
        };
        $year = date('y');
        
        $lastAsset = Asset::where('asset_tag', 'LIKE', "$prefix-$year-%")
                        ->orderBy('id', 'desc')->first();       
        $sequence = $lastAsset ? intval(substr($lastAsset->asset_tag, -3)) + 1 : 1;
        $newTag = sprintf("%s-%s-%03d", $prefix, $year, $sequence);
        
        // Langsung buat Aset di tabel utama
        $asset = Asset::create([
            'name' => $request->name,
            'category' => $request->category,
            'asset_tag' => $newTag,
            'status' => $request->status,            
            'purchase_date' => $request->purchase_date,
            'warranty_expiry_date' => $request->warranty_expiry_date,
            'condition' => $request->condition,
            'person_in_charge' => $request->person_in_charge,
            'location' => $request->location,
            'vendor' => $request->vendor,
            'serial_number' => $request->serial_number,
            'description' => $request->description,
        ]);

        $this->storeAttachments($request, $asset, Auth::id());

        $user = \Illuminate\Support\Facades\Auth::user();

        // Catat ke Log dengan status langsung APPROVED tanpa mempedulikan Role
        AssetLog::create([
            'asset_id' => $asset->id,
            'user_id' => $user->id,
            'action' => 'create',
            'new_data' => $asset->toArray(),
            'status' => 'approved',
            'approved_by' => $user->id, // Dianggap disetujui oleh si pembuat itu sendiri
            'approved_at' => now(),
        ]);

        return redirect()->route('assets.index')->with('success', 'Aset Baru Berhasil Ditambah: ' . $newTag);
    }

    public function edit($id)
    {
        $asset = Asset::with('attachments')->findOrFail($id);
        return view('assets.edit', compact('asset'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required|in:mobile,semi-mobile,fixed',
            'description' => 'required',
            'status' => 'required|in:in_use,maintenance,broken,available',
            'purchase_date' => 'required',
            'condition' => 'required',
            'location' => 'nullable|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'warranty_expiry_date' => 'nullable|date',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        $asset = Asset::findOrFail($id);
        $user = Auth::user();

        $newData = [
            'name' => $request->name,
            'category' => $request->category,
            'status' => $request->status,
            'description' => $request->description,
            'purchase_date' => $request->purchase_date,
            'warranty_expiry_date' => $request->warranty_expiry_date,
            'condition' => $request->condition,
            'person_in_charge' => $request->person_in_charge,
            'location' => $request->location,
            'vendor' => $request->vendor,
            'serial_number' => $request->serial_number,
        ];

        if ($user->role === 'super_admin') {
            // === SUPER ADMIN: Langsung Ubah Data & Auto Approve ===
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

            return redirect()->route('dashboard')->with('success', 'Data Aset Berhasil Diperbarui!');
        } else {
            // === ADMIN: Masuk Antrean Log (Data Asli Tidak Berubah Dulu) ===
            AssetLog::create([
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'action' => 'update',
                'old_data' => $asset->toArray(),
                'new_data' => $newData,
                'status' => 'pending', // Status Log-nya Pending
            ]);

            return redirect()->route('dashboard')->with('success', 'Permintaan update aset telah dikirim dan menunggu persetujuan Super Admin.');
        }
    }

    // Hapus Aset
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $user = Auth::user();

        if ($user->role === 'super_admin') {
            // Super Admin: Langsung Hapus
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
            return redirect()->route('dashboard')->with('success', 'Aset Berhasil Dihapus.');
        } else {
            // Admin: Request Hapus
            AssetLog::create([
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'action' => 'delete',
                'old_data' => $asset->toArray(),
                'status' => 'pending',
            ]);
            
            return redirect()->route('dashboard')->with('success', 'Permintaan hapus aset dikirim ke Super Admin.');
        }
    }

    // Export Laporan Aset
    public function export()
    {
        return $this->exportReport();
    }

    // Export Laporan Aset
    public function exportReport()
    {
        // Ambil semua data aset, urutkan berdasarkan kategori lalu nama
        $assets = Asset::orderBy('category')->orderBy('name')->get();
        
        // Load view PDF yang baru kita buat
        $pdf = Pdf::loadView('assets.pdf_report', compact('assets'));
        
        // Set ukuran kertas jadi Landscape agar muat banyak kolom
        $pdf->setPaper('a4', 'landscape');
        
        // Download file
        return $pdf->stream('Vodeco.pdf');
    }

    // Preview & Cetak Label
    public function printPreview(Request $request)
    {
        $query = Asset::query();

        // 1. Logic Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('asset_tag', 'like', '%' . $search . '%')
                ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        $assets = $query->latest()->paginate(10)->withQueryString();

        // 2. JIKA AJAX (Live Search), RETURN PARTIAL VIEW
        if ($request->ajax()) {
            return view('assets.partials.print-table', compact('assets'))->render();
        }

        // 3. JIKA BIASA, RETURN FULL PAGE
        return view('assets.print_preview', compact('assets'));
    }

    //
    public function downloadPdf(Request $request)
    {
        if ($request->has('selected_assets')) {
            // MODE 1: CETAK SELEKTIF (CHECKLIST)
            $assets = Asset::whereIn('id', $request->selected_assets)
                            ->orderBy('id', 'asc')
                            ->get();
        } else {
            // MODE 2: CETAK SEMUA (TOMBOL HITAM)
            $assets = Asset::orderBy('id', 'asc')->get();
        }

        // Generate QR Code untuk setiap aset terpilih
        foreach ($assets as $asset) {
            $asset->qr_code = base64_encode(QrCode::format('png')->size(100)->generate($asset->asset_tag));
        }

        // Load view PDF
        $pdf = Pdf::loadView('assets.pdf_label', compact('assets'));
        
        // Set ukuran kertas custom (contoh: ukuran label sticker) atau A4
        $pdf->setPaper('a4', 'portrait');

        // Stream (Preview dulu di browser, jangan langsung download)
        return $pdf->stream('labels-vodeco-selected.pdf');
    }

    // 1. DASHBOARD: Pagination + Search
    public function index(Request $request)
    {
        $query = Asset::query()->with('activeTransaction');

        $this->applyAssetFilters($request, $query);

        $assets = $query->latest()->paginate(10)->withQueryString();

        // Jika request datang dari AJAX, kembalikan hanya bagian tabelnya saja
        if ($request->ajax()) {
            return view('assets.partials.table', compact('assets'))->render();
        }

        // Jika request biasa, kembalikan halaman index aset
        return view('assets.index', compact('assets'));
    }

    /**
     * Update status cepat dari halaman index (web).
     */
    public function updateStatusFromWeb(Request $request)
    {
        $request->validate([
            'asset_tag' => 'required|exists:assets,asset_tag',
            'action' => 'required|in:check_in,check_out,maintenance',
            'borrower_name' => 'nullable|string|max:255',
            'due_at' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        $asset = Asset::where('asset_tag', $request->asset_tag)->firstOrFail();
        $oldData = $asset->toArray();
        $user = Auth::user();

        if ($request->action === 'check_out') {
            $newStatus = 'in_use';
            $message = 'Aset berhasil dipinjam/digunakan.';
        } elseif ($request->action === 'check_in') {
            $newStatus = 'available';
            $message = 'Aset berhasil dikembalikan.';
        } else {
            $newStatus = 'maintenance';
            $message = 'Aset masuk maintenance.';
        }

        DB::transaction(function () use ($asset, $newStatus, $request, $oldData, $user): void {
            $asset->update(['status' => $newStatus]);

            if ($request->action === 'check_out') {
                AssetTransaction::create([
                    'asset_id' => $asset->id,
                    'borrower_user_id' => $user->id,
                    'borrower_name' => $request->borrower_name ?: $user->name,
                    'borrowed_at' => now(),
                    'due_at' => $request->due_at,
                    'notes' => $request->notes,
                    'created_by' => $user->id,
                    'status' => 'borrowed',
                ]);
            }

            if ($request->action === 'check_in') {
                $activeTransaction = $asset->transactions()
                    ->whereNull('returned_at')
                    ->latest('borrowed_at')
                    ->first();

                if ($activeTransaction) {
                    $returnedAt = now();
                    $durationDays = max(1, Carbon::parse($activeTransaction->borrowed_at)->diffInDays($returnedAt));

                    $activeTransaction->update([
                        'returned_at' => $returnedAt,
                        'duration_days' => $durationDays,
                        'status' => 'returned',
                        'notes' => $request->notes ?: $activeTransaction->notes,
                    ]);
                }
            }

            AssetLog::create([
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'action' => $request->action,
                'old_data' => $oldData,
                'new_data' => array_merge($asset->fresh()->toArray(), [
                    'borrower_name' => $request->borrower_name,
                    'due_at' => $request->due_at,
                    'notes' => $request->notes,
                ]),
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
        });

        return redirect()->route('assets.index')->with('success', $message);
    }

    // Method Selection untuk Menu "Print QR Code"
    public function print_selection(Request $request)
    {
        $search = $request->input('search');

        $assets = Asset::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                            ->orWhere('asset_tag', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10) // Max Paginate 10
            ->withQueryString();

        return view('assets.print', compact('assets'));
    }

    /**
     * API: Mengambil semua ID aset tanpa pagination
     * Mendukung filter search untuk konsistensi dengan printPreview
     */
    public function getAllAssetIds(Request $request)
    {
        $query = Asset::query();

        $this->applyAssetFilters($request, $query);

        // Ambil hanya kolom id untuk efisiensi
        $assetIds = $query->pluck('id')->toArray();

        return response()->json([
            'success' => true,
            'total' => count($assetIds),
            'ids' => $assetIds
        ]);
    }

    // FITUR APPROVAL QUEUE (MAKER-CHECKER)
    // 1. Tampilkan Halaman Antrean
    public function approvalQueue()
    {
        // Pastikan hanya Super Admin yang bisa masuk
        if (Auth::user()->role !== 'super_admin') {
            abort(403, 'Akses Ditolak. Hanya Super Admin yang dapat melihat halaman ini.');
        }

        // Ambil data log yang masih 'pending'
        $pendingLogs = \App\Models\AssetLog::with(['user', 'asset'])
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

    // 2. Logika Setujui (Approve)
    public function approve($id)
    {
        if (Auth::user()->role !== 'super_admin') abort(403);

        $log = \App\Models\AssetLog::findOrFail($id);
        $asset = Asset::find($log->asset_id);

        if (! $asset && $log->action !== 'delete') {
            return back()->with('error', 'Aset terkait tidak ditemukan.');
        }

        // Eksekusi perubahan ke tabel utama (assets) berdasarkan tipe action
        if ($log->action === 'update') {
            $asset->update($log->new_data ?? []);
        } elseif ($log->action === 'delete') {
            $asset?->delete();
        }

        // Ubah status tiket log menjadi approved
        $log->update([
            'status' => 'approved',
            'approved_by' => Auth::user()->id,
            'approved_at' => now()
        ]);

        return back()->with('success', 'Tiket permintaan ' . strtoupper($log->action) . ' berhasil disetujui!');
    }

    // 3. Logika Tolak (Reject)
    public function reject(Request $request, $id)
    {
        if (Auth::user()->role !== 'super_admin') abort(403);

        $log = \App\Models\AssetLog::findOrFail($id);
        
        // Ubah status tiket log menjadi rejected (tabel assets tidak disentuh)
        $log->update([
            'status' => 'rejected',
            'approved_by' => Auth::user()->id,
            'approved_at' => now(),
            'rejection_note' => $request->rejection_note ?? 'Ditolak oleh Super Admin tanpa catatan.'
        ]);

        return back()->with('error', 'Tiket permintaan ' . strtoupper($log->action) . ' telah ditolak.');
    }

    public function history(Request $request)
    {
        $transactions = AssetTransaction::with(['asset', 'borrower'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('borrowed_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('borrowed_at', '<=', $request->date_to);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('borrower_name', 'like', '%' . $search . '%')
                        ->orWhereHas('asset', function ($assetQuery) use ($search) {
                            $assetQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('asset_tag', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest('borrowed_at')
            ->paginate(15)
            ->withQueryString();

        return view('assets.history', compact('transactions'));
    }

    protected function applyAssetFilters(Request $request, $query): void
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

    protected function storeAttachments(Request $request, Asset $asset, ?int $uploadedBy = null): void
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