<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CompanyAsset;
use App\Models\AssetCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AssetRegisterController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyAsset::with('category');

        if ($request->asset_category_id) {
            $query->where('asset_category_id', $request->asset_category_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $assets = $query->orderBy('asset_number')->get();
        $categories = AssetCategory::orderBy('name')->get();

        $totals = [
            'total_cost' => $assets->sum('purchase_cost'),
            'total_depreciation' => $assets->sum('accumulated_depreciation'),
            'total_book_value' => $assets->sum('current_book_value'),
        ];

        return view('reports.asset.index', compact(
            'assets', 'categories', 'totals'
        ));
    }

    public function export(Request $request)
    {
        $query = CompanyAsset::with('category');

        if ($request->asset_category_id) {
            $query->where('asset_category_id', $request->asset_category_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $assets = $query->orderBy('asset_number')->get();
        $categories = AssetCategory::orderBy('name')->get();

        $totals = [
            'total_cost' => $assets->sum('purchase_cost'),
            'total_depreciation' => $assets->sum('accumulated_depreciation'),
            'total_book_value' => $assets->sum('current_book_value'),
        ];

        $pdf = Pdf::loadView('reports.asset.pdf', compact(
            'assets', 'categories', 'totals'
        ));

        return $pdf->download('asset-register.pdf');
    }
}