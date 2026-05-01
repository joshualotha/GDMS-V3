<?php

namespace App\Http\Controllers\Fuel;

use App\Http\Controllers\Controller;
use App\Models\FuelAsset;
use App\Models\FuelIssue;
use App\Services\FuelService;
use Illuminate\Http\Request;

class FuelIssueController extends Controller
{
    protected $fuelService;

    public function __construct(FuelService $fuelService)
    {
        $this->fuelService = $fuelService;
    }

    public function index()
    {
        $issues = FuelIssue::with('asset')->orderBy('date', 'desc')->get();

        return view('fuel.issues.index', compact('issues'));
    }

    public function create()
    {
        $assets = FuelAsset::where('type', 'car')->where('is_active', true)->orderBy('name')->get();

        return view('fuel.issues.create', compact('assets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'asset_id' => 'required|exists:fuel_assets,id',
            'fuel_type' => 'required|in:diesel,petrol',
            'litres' => 'required|numeric|min:0.01',
            'odometer_km' => 'nullable|integer',
            'issued_by' => 'nullable|string',
        ]);

        try {
            $this->fuelService->issueFuel($validated);

            return redirect()->route('fuel.issues.index')
                ->with('success', 'Fuel issued.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
