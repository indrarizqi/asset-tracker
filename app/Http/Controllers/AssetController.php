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
        ]);

        $prefix = match($request->category) {
            'mobile' => 'M',
            'semi-mobile' => 'SM',
            'fixed' => 'F',
        };
        $year = date('y');
        
        // Auto-Numbering
        $lastAsset = Asset::where('asset_tag', 'LIKE', "$prefix-$year-%")
                        ->orderBy('id', 'desc')->first();       
        $sequence = $lastAsset ? intval(substr($lastAsset->asset_tag, -3)) + 1 : 1;
        $newTag = sprintf("%s-%s-%03d", $prefix, $year, $sequence);

        // Jika PJ diisi -> Status 'in_use'. Jika kosong -> 'available'
        $status = $request->filled('person_in_charge') ? 'in_use' : 'available';

        Asset::create([
            'name' => $request->name,
            'category' => $request->category,
            'asset_tag' => $newTag,
            'status' => $status, // Gunakan variabel status dinamis di atas
            
            // Data Tambahan
            'purchase_date' => $request->purchase_date,
            'asset_condition' => $request->asset_condition,
            'person_in_charge' => $request->person_in_charge,
            
        ]);

        return redirect()->route('dashboard')->with('success', 'Aset berhasil ditambah: ' . $newTag . ' (' . ucfirst($status) . ')');
    }

    // Preview & Cetak Label
    public function printPreview()
    {
        $assets = Asset::latest()->get(); // Ambil semua aset
        return view('assets.print_preview', compact('assets'));
    }

    public function downloadPdf(Request $request)
    {
        // Cek apakah ada ID yang dipilih dari checkbox?
        if ($request->has('ids')) {
            // Ambil HANYA aset yang ID-nya ada di list checkbox
            $assets = Asset::whereIn('id', $request->ids)->get();
        } else {
            // Fallback: Ambil semua jika tidak ada filter (opsional)
            $assets = Asset::all();
        }

        if ($assets->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada aset yang dipilih.');
        }
        
        // Generate QR Code untuk setiap aset terpilih
        foreach ($assets as $asset) {
            // Kita gunakan library simple-qrcode
            $asset->qr_code = base64_encode(QrCode::format('png')->size(100)->generate($asset->asset_tag));
        }

        $pdf = Pdf::loadView('assets.pdf_label', compact('assets'));
        
        // Tips: Gunakan 'stream' agar bisa preview dulu di browser, bukan langsung 'download'
        return $pdf->stream('labels-vodeco-selected.pdf');
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
            'status' => 'required',
        ]);

        $asset = Asset::findOrFail($id);
        $asset->update([
            'name' => $request->name,
            'category' => $request->category, // Kategori boleh diedit
            'status' => $request->status,     // Status boleh diedit manual admin
        ]);

        return redirect()->route('dashboard')->with('success', 'Data aset berhasil diperbarui!');
    }

    // Hapus Aset (Untuk Super Admin)
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
        return redirect()->route('dashboard')->with('success', 'Aset berhasil dihapus.');
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
        return $pdf->stream('Laporan-Aset-Vodeco.pdf');
    }

    public function index()
    {
    // Ambil data aset, urutkan dari yang terbaru
    $assets = Asset::latest()->get();
    
    // Tampilkan view dashboard dengan membawa data assets
    return view('dashboard', compact('assets'));
    }
}