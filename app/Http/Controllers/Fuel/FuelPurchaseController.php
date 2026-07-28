<?php

namespace App\Http\Controllers\Fuel;

use App\Http\Controllers\Controller;
use App\Models\FuelPurchase;
use App\Models\FuelStock;
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
        $purchases = FuelPurchase::with('supplierAccount')->orderBy('date', 'desc')->get();
        return view('fuel.purchases.index', compact('purchases'));
    }

    public function stock()
    {
        $fuelStock = FuelStock::all()->keyBy('fuel_type');
        return view('fuel.purchases.stock', compact('fuelStock'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        return view('fuel.purchases.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'fuel_type' => 'required|in:diesel,petrol',
            'litres' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'receipt_number' => 'nullable|string',
        ]);

        $this->fuelService->recordPurchase($validated);

        return redirect()->route('fuel.purchases.index')
            ->with('success', 'Fuel purchase recorded.');
    }
}