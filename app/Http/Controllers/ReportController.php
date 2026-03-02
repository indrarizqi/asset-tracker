<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Asset;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReportController extends Controller
{
    /**
     * Export laporan aset ke PDF.
     */
    public function exportReport(): HttpResponse
    {
        $assets = Asset::orderBy('category')->orderBy('name')->get();

        $pdf = Pdf::loadView('assets.pdf_report', compact('assets'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Vodeco.pdf');
    }

    /**
     * Preview & cetak label QR Code.
     */
    public function printPreview(Request $request): View|string
    {
        $query = Asset::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('asset_tag', 'like', '%' . $search . '%')
                    ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        $assets = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('assets.partials.print-table', compact('assets'))->render();
        }

        return view('assets.print_preview', compact('assets'));
    }

    /**
     * Download PDF label QR Code (selektif atau semua).
     */
    public function downloadPdf(Request $request): HttpResponse
    {
        if ($request->has('selected_assets')) {
            $assets = Asset::whereIn('id', $request->selected_assets)
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $assets = Asset::orderBy('id', 'asc')->get();
        }

        foreach ($assets as $asset) {
            $asset->qr_code = base64_encode(QrCode::format('png')->size(100)->generate($asset->asset_tag));
        }

        $pdf = Pdf::loadView('assets.pdf_label', compact('assets'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('labels-vodeco-selected.pdf');
    }
}
