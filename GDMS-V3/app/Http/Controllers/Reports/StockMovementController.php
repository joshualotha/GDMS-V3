<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\StockMainLedger;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $query = StockMainLedger::whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($request->transaction_type) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->cylinder_type_id) {
            $query->where('cylinder_type_id', $request->cylinder_type_id);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();

        $totals = [
            'full_change' => $movements->sum('full_qty_change'),
            'empty_change' => $movements->sum('empty_qty_change'),
        ];

        return view('reports.stock-movement.index', compact(
            'movements', 'cylinderTypes', 'totals', 'dateFrom', 'dateTo'
        ));
    }

    public function export(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfDay();

        $query = StockMainLedger::whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($request->transaction_type) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->cylinder_type_id) {
            $query->where('cylinder_type_id', $request->cylinder_type_id);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();

        $totals = [
            'full_change' => $movements->sum('full_qty_change'),
            'empty_change' => $movements->sum('empty_qty_change'),
        ];

        $pdf = Pdf::loadView('reports.stock-movement.pdf', compact(
            'movements', 'cylinderTypes', 'totals', 'dateFrom', 'dateTo'
        ));

        return $pdf->download('stock-movement-' . $dateFrom->format('Y-m-d') . '-to-' . $dateTo->format('Y-m-d') . '.pdf');
    }
}