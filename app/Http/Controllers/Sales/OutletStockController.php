<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\Outlet;
use App\Models\StockMain;
use App\Models\StockOutlet;
use Illuminate\Http\Request;

class OutletStockController extends Controller
{
    public function index(Request $request)
    {
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();

        $mainStock = StockMain::all()->keyBy('cylinder_type_id');

        $stockData = [];
        foreach ($outlets as $outlet) {
            $stockData[$outlet->id] = StockOutlet::where('outlet_id', $outlet->id)->get()->keyBy('cylinder_type_id');
        }

        return view('sales.outlet-stock.index', compact('outlets', 'cylinderTypes', 'stockData', 'mainStock'));
    }
}
