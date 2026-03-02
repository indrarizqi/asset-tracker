<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = Asset::selectRaw("
            COUNT(*) as total_assets,
            COALESCE(SUM(price), 0) as total_value,
            SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as status_available,
            SUM(CASE WHEN status = 'in_use' THEN 1 ELSE 0 END) as status_in_use,
            SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as status_maintenance,
            SUM(CASE WHEN status = 'broken' THEN 1 ELSE 0 END) as status_broken,
            SUM(CASE WHEN category = 'mobile' THEN 1 ELSE 0 END) as cat_mobile,
            SUM(CASE WHEN category = 'semi-mobile' THEN 1 ELSE 0 END) as cat_semi_mobile,
            SUM(CASE WHEN category = 'fixed' THEN 1 ELSE 0 END) as cat_fixed,
            SUM(CASE WHEN `condition` LIKE '%baik%' THEN 1 ELSE 0 END) as cond_baik,
            SUM(CASE WHEN `condition` LIKE '%rusak%' AND `condition` NOT LIKE '%total%' AND `condition` NOT LIKE '%berat%' THEN 1 ELSE 0 END) as cond_rusak,
            SUM(CASE WHEN `condition` LIKE '%total%' OR `condition` LIKE '%berat%' THEN 1 ELSE 0 END) as cond_rusak_total
        ")->first();

        $pendingCount = AssetLog::where('status', 'pending')->count();
        $logs = AssetLog::with(['user', 'asset'])->latest()->paginate(4);

        return view('dashboard', [
            'totalAssets' => (int) $stats->total_assets,
            'totalValue' => (int) $stats->total_value,
            'pendingCount' => $pendingCount,
            'statusAvailable' => (int) $stats->status_available,
            'statusInUse' => (int) $stats->status_in_use,
            'statusMaintenance' => (int) $stats->status_maintenance,
            'statusBroken' => (int) $stats->status_broken,
            'catMobile' => (int) $stats->cat_mobile,
            'catSemiMobile' => (int) $stats->cat_semi_mobile,
            'catFixed' => (int) $stats->cat_fixed,
            'condBaik' => (int) $stats->cond_baik,
            'condRusak' => (int) $stats->cond_rusak,
            'condRusakTotal' => (int) $stats->cond_rusak_total,
            'logs' => $logs,
        ]);
    }
}
