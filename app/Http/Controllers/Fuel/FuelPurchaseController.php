<?php

namespace App\Http\Controllers\Fuel;

use App\Http\Controllers\Controller;
use App\Models\FuelPurchase;
use App\Models\Outlet;
use App\Models\Supplier;
use App\Services\FuelService;
use Illuminate\Http\Request;

class FuelPurchaseController extends Controller
{
    protected $fuelService;

    public function __construct(FuelService $fuelService)
    {
        $this->fuelService = $fuelService;
    }

    public function index()
    {
        $purchases = FuelPurchase::with('supplierAccount', 'outlet')->orderBy('date', 'desc')->get();
        return view('fuel.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $vehicles = Outlet::where('type', 'car')->where('is_active', true)->orderBy('name')->get();
        return view('fuel.purchases.create', compact('suppliers', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'outlet_id' => 'required|exists:outlets,id',
            'odometer_km' => 'required|integer|min:0',
            'fuel_type' => 'required|in:diesel,petrol',
            'litres' => 'required|numeric|min:0.01',
            'total_cost' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'receipt_number' => 'nullable|string',
        ]);

        $this->fuelService->recordPurchase($validated);

        return redirect()->route('fuel.purchases.index')
            ->with('success', 'Fuel purchase recorded.');
    }

    public function destroy(FuelPurchase $fuelPurchase)
    {
        try {
            $this->fuelService->deletePurchase($fuelPurchase);

            return redirect()->route('fuel.purchases.index')
                ->with('success', 'Fuel purchase deleted and stock reversed.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}