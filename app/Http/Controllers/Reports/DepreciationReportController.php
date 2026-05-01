<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CompanyAsset;
use App\Models\DepreciationLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DepreciationReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $query = DepreciationLog::whereBetween('created_at', [$dateFrom, $dateTo])->with('asset');

        if ($request->asset_id) {
            $query->where('asset_id', $request->asset_id);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();
        $assets = CompanyAsset::orderBy('name')->get();

        // Build annual summary per asset
        $summary = [];
        foreach ($logs->groupBy('asset_id') as $assetId => $assetLogs) {
            $asset = $assetLogs->first()->asset;
            $totalDepreciation = $assetLogs->sum('depreciation_amount');
            
            // Get opening from first log (chronologically)
            $firstLog = $assetLogs->sortBy('created_at')->first();
            // Get closing from current asset value
            $currentBookValue = $asset->current_book_value ?? $firstLog->book_value_before - $totalDepreciation;
            
            $summary[] = (object) [
                'asset_name' => $asset->name ?? 'Unknown',
                'annual_rate' => $firstLog->depreciation_rate ?? 0,
                'opening_book_value' => $firstLog->book_value_before,
                'total_depreciation' => $totalDepreciation,
                'closing_book_value' => $currentBookValue,
                'entry_count' => $assetLogs->count(),
            ];
        }

        return view('reports.depreciation.index', compact(
            'logs', 'assets', 'dateFrom', 'dateTo', 'summary'
        ));
    }

    public function export(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $query = DepreciationLog::whereBetween('created_at', [$dateFrom, $dateTo])->with('asset');

        if ($request->asset_id) {
            $query->where('asset_id', $request->asset_id);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();
        $assets = CompanyAsset::orderBy('name')->get();

        // Build annual summary per asset
        $summary = [];
        foreach ($logs->groupBy('asset_id') as $assetId => $assetLogs) {
            $asset = $assetLogs->first()->asset;
            $totalDepreciation = $assetLogs->sum('depreciation_amount');
            
            $firstLog = $assetLogs->sortBy('created_at')->first();
            $currentBookValue = $asset->current_book_value ?? $firstLog->book_value_before - $totalDepreciation;
            
            $summary[] = (object) [
                'asset_name' => $asset->name ?? 'Unknown',
                'annual_rate' => $firstLog->depreciation_rate ?? 0,
                'opening_book_value' => $firstLog->book_value_before,
                'total_depreciation' => $totalDepreciation,
                'closing_book_value' => $currentBookValue,
                'entry_count' => $assetLogs->count(),
            ];
        }

        $pdf = Pdf::loadView('reports.depreciation.pdf', compact(
            'logs', 'assets', 'dateFrom', 'dateTo', 'summary'
        ));

        return $pdf->download('depreciation-report-' . $dateFrom->format('Y-m-d') . '-to-' . $dateTo->format('Y-m-d') . '.pdf');
    }
}