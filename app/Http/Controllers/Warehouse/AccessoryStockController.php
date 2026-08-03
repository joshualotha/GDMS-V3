<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Accessory;
use App\Models\Outlet;
use App\Models\StockMainAccessory;
use App\Models\StockOutletAccessory;
use Illuminate\Http\Request;

class AccessoryStockController extends Controller
{
    public function index(Request $request)
    {
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        $accessories = Accessory::where('is_active', true)->orderBy('name')->get();

        $mainStock = StockMainAccessory::all()->keyBy('accessory_id');

        $stockData = [];
        foreach ($outlets as $outlet) {
            $stockData[$outlet->id] = StockOutletAccessory::where('outlet_id', $outlet->id)->get()->keyBy('accessory_id');
        }

        return view('warehouse.accessory-stock.index', compact('outlets', 'accessories', 'stockData', 'mainStock'));
    }
}
