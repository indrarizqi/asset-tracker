<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf; // Import facade PDF

class AssetController extends Controller
{
    public function create()
    {
        return view('assets.create');
    }

    // Simpan Data & Generate Auto ID
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required|in:mobile,semi-mobile,fixed',
            'description' => 'required',
            'status' => 'required|in:in_use,maintenance,broken,available',
            'purchase_date' => 'required',
            'condition' => 'required',
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
        
        $status = $request->status;

        Asset::create([
            'name' => $request->name,
            'category' => $request->category,
            'asset_tag' => $newTag,
            'status' => $status,            
            'purchase_date' => $request->purchase_date,
            'condition' => $request->condition,
            'person_in_charge' => $request->person_in_charge,
            'description' => $request->description,
        ]);

        return redirect()->route('dashboard')->with('success', 'Aset Berhasil Ditambah : ' . $newTag . ' (' . ucfirst($status) . ')');
    }

    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
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
        ]);

        $asset = Asset::findOrFail($id);
        $asset->update([
            'name' => $request->name,
            'category' => $request->category,
            'status' => $request->status,
            'description' => $request->description,
            'purchase_date' => $request->purchase_date,
            'condition' => $request->condition,
            'person_in_charge' => $request->person_in_charge,
        ]);

        return redirect()->route('dashboard')->with('success', 'Data Aset Berhasil Diperbarui!');
    }

    // Hapus Aset (Untuk Super Admin)
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
        return redirect()->route('dashboard')->with('success', 'Aset Berhasil Dihapus.');
    }

    // Export Laporan Aset (Untuk Super Admin)
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
        $selectedIds = $request->input('selected_assets');
        
        if ($request->has('selected_assets')) {
            // MODE 1: CETAK SELEKTIF (CHECKLIST)
            $assets = Asset::whereIn('id', $request->selected_assets)
                            ->orderBy('id', 'asc')
                            ->get();
        } else {
            // MODE 2: CETAK SEMUA (TOMBOL HITAM)
            $assets = Asset::orderBy('id', 'asc')->get();
        }

        // Load view PDF
        $pdf = pdf::loadView('assets.pdf_label', compact('assets'));
        
        // Set ukuran kertas custom (contoh: ukuran label sticker) atau A4
        $pdf->setPaper('a4', 'portrait');

        // Stream (Preview dulu di browser, jangan langsung download)
        return $pdf->stream('Vodeco.pdf');

        // Generate QR Code untuk setiap aset terpilih
        foreach ($assets as $asset) {
            // Kita gunakan library simple-qrcode
            $asset->qr_code = base64_encode(QrCode::format('png')->size(100)->generate($asset->asset_tag));
        }

        $pdf = Pdf::loadView('assets.pdf_label', compact('assets'));
        
        // Tips: Gunakan 'stream' agar bisa preview dulu di browser, bukan langsung 'download'
        return $pdf->stream('labels-vodeco-selected.pdf');
    }

    // 1. DASHBOARD: Pagination + Search
    public function index(Request $request)
    {
        $query = Asset::query();

        // Optimasi Query Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('asset_tag', 'like', '%' . $search . '%')
                ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        $assets = $query->latest()->paginate(10)->withQueryString();

        // Jika request datang dari AJAX, kembalikan hanya bagian tabelnya saja
        if ($request->ajax()) {
            return view('assets.partials.table', compact('assets'))->render();
        }

        // Jika request biasa, kembalikan halaman dashboard utuh
        return view('dashboard', compact('assets'));
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

        // Terapkan filter search yang sama dengan printPreview
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('asset_tag', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        // Ambil hanya kolom id untuk efisiensi
        $assetIds = $query->pluck('id')->toArray();

        return response()->json([
            'success' => true,
            'total' => count($assetIds),
            'ids' => $assetIds
        ]);
    }
}