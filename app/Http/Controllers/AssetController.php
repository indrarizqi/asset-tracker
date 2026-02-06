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

    // simpan data & generate auto ID
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
        
        $lastAsset = Asset::where('asset_tag', 'LIKE', "$prefix-$year-%")
                          ->orderBy('id', 'desc')->first();
                          
        $sequence = $lastAsset ? intval(substr($lastAsset->asset_tag, -3)) + 1 : 1;
        $newTag = sprintf("%s-%s-%03d", $prefix, $year, $sequence);

        Asset::create([
            'name' => $request->name,
            'category' => $request->category,
            'asset_tag' => $newTag,
            'status' => 'available',

            // Revisi Form Baru
            'purchase_date' => $request->purchase_date,
            'asset_condition' => $request->asset_condition,
            'person_in_charge' => $request->person_in_charge,
        ]);

        return redirect('/assets/print')->with('success', 'Aset berhasil ditambah: ' . $newTag);
    }

    // preview & dan cetak label
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

    // 1. Tampilkan Form Edit
    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        return view('assets.edit', compact('asset'));
    }

    // 2. Simpan Perubahan
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

    // 3. Hapus Aset (Untuk Super Admin)
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
        return redirect()->route('dashboard')->with('success', 'Aset berhasil dihapus.');
    }

    public function index()
    {
    // Ambil data aset, urutkan dari yang terbaru
    $assets = Asset::latest()->get();
    
    // Tampilkan view dashboard dengan membawa data assets
    return view('dashboard', compact('assets'));
    }
}