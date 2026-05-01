<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\Outlet;
use App\Models\StockMain;
use App\Models\StockMainLedger;
use App\Models\StockOutlet;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date)->endOfDay() : now()->endOfDay();
        
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();
        $outlets = Outlet::orderBy('name')->get();
        
        $mainStock = [];
        foreach ($cylinderTypes as $ct) {
            $mainStock[$ct->id] = $this->getStockAtDate($ct->id, null, $date);
        }
        
        $outletStock = [];
        foreach ($outlets as $outlet) {
            $outletStock[$outlet->id] = [];
            foreach ($cylinderTypes as $ct) {
                $outletStock[$outlet->id][$ct->id] = $this->getOutletStockAtDate($outlet->id, $ct->id, $date);
            }
        }
        
        $totalFull = array_sum(array_column($mainStock, 'full'));
        $totalEmpty = array_sum(array_column($mainStock, 'empty'));
        
        return view('reports.stock.index', compact(
            'cylinderTypes', 'outlets', 'mainStock', 'outletStock', 
            'totalFull', 'totalEmpty', 'date'
        ));
    }

    public function export(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date)->endOfDay() : now()->endOfDay();
        
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();
        $outlets = Outlet::orderBy('name')->get();
        
        $mainStock = [];
        foreach ($cylinderTypes as $ct) {
            $mainStock[$ct->id] = $this->getStockAtDate($ct->id, null, $date);
        }
        
        $outletStock = [];
        foreach ($outlets as $outlet) {
            $outletStock[$outlet->id] = [];
            foreach ($cylinderTypes as $ct) {
                $outletStock[$outlet->id][$ct->id] = $this->getOutletStockAtDate($outlet->id, $ct->id, $date);
            }
        }
        
        $totalFull = array_sum(array_column($mainStock, 'full'));
        $totalEmpty = array_sum(array_column($mainStock, 'empty'));

        $pdf = Pdf::loadView('reports.stock.pdf', compact(
            'cylinderTypes', 'outlets', 'mainStock', 'outletStock',
            'totalFull', 'totalEmpty', 'date'
        ));

        return $pdf->download('stock-report-' . $date->format('Y-m-d') . '.pdf');
    }

    private function getStockAtDate($cylinderTypeId, $outletId, $date)
    {
        $query = StockMain::where('cylinder_type_id', $cylinderTypeId);
        
        if ($outletId) {
            $query = StockOutlet::where('outlet_id', $outletId)
                ->where('cylinder_type_id', $cylinderTypeId);
        }

        if ($date->lt(now())) {
            $ledger = StockMainLedger::where('cylinder_type_id', $cylinderTypeId)
                ->where('created_at', '<=', $date)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($ledger) {
                return ['full' => $ledger->full_qty_after, 'empty' => $ledger->empty_qty_after];
            }
        }

        $stock = $query->first();
        return $stock ? ['full' => $stock->full_qty, 'empty' => $stock->empty_qty] : ['full' => 0, 'empty' => 0];
    }

    private function getOutletStockAtDate($outletId, $cylinderTypeId, $date)
    {
        $query = StockOutlet::where('outlet_id', $outletId)
            ->where('cylinder_type_id', $cylinderTypeId);

        if ($date->lt(now())) {
            $ledger = StockMainLedger::where('reference_type', 'StockOutlet')
                ->where('reference_id', $outletId)
                ->where('cylinder_type_id', $cylinderTypeId)
                ->where('created_at', '<=', $date)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($ledger) {
                return ['full' => $ledger->full_qty_after, 'empty' => $ledger->empty_qty_after];
            }
        }

        $stock = $query->first();
        return $stock ? ['full' => $stock->full_qty, 'empty' => $stock->empty_qty] : ['full' => 0, 'empty' => 0];
    }
}