<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\Outlet;
use App\Models\StockMain;
use App\Models\StockMainLedger;
use Illuminate\Http\Request;

class StockMainController extends Controller
{
    public function index(Request $request)
    {
        $stockMain = StockMain::with('cylinderType')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();

        $query = StockMainLedger::with('cylinderType');

        if ($request->has('location') && $request->location) {
            if ($request->location == 'main') {
                $query->whereNull('outlet_id');
            } else {
                $query->where('outlet_id', $request->location);
            }
        }

        if ($request->has('cylinder_type_id') && $request->cylinder_type_id) {
            $query->where('cylinder_type_id', $request->cylinder_type_id);
        }

        if ($request->has('transaction_type') && $request->transaction_type) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $ledger = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('warehouse.stock-ledger.index', compact('stockMain', 'cylinderTypes', 'outlets', 'ledger'));
    }
}
