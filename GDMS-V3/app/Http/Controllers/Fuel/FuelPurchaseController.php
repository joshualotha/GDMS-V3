<?php

namespace App\Http\Controllers\Fuel;

use App\Http\Controllers\Controller;
use App\Models\FuelPurchase;
use App\Models\FuelStock;
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
        $purchases = FuelPurchase::orderBy('date', 'desc')->get();
        return view('fuel.purchases.index', compact('purchases'));
    }

    public function stock()
    {
        $fuelStock = FuelStock::all()->keyBy('fuel_type');
        return view('fuel.purchases.stock', compact('fuelStock'));
    }

    public function create()
    {
        return view('fuel.purchases.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'fuel_type' => 'required|in:diesel,petrol',
            'litres' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
            'supplier' => 'nullable|string',
            'receipt_number' => 'nullable|string',
        ]);

        $this->fuelService->recordPurchase($validated);

        return redirect()->route('fuel.purchases.index')
            ->with('success', 'Fuel purchase recorded.');
    }
}