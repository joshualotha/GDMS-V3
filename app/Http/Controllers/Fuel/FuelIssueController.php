<?php

namespace App\Http\Controllers\Fuel;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
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
        $issues = FuelIssue::with('outlet')->orderBy('date', 'desc')->get();

        return view('fuel.issues.index', compact('issues'));
    }

    public function create()
    {
        $outlets = Outlet::where('type', 'car')->where('is_active', true)->orderBy('name')->get();

        return view('fuel.issues.create', compact('outlets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'outlet_id' => 'required|exists:outlets,id',
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

    public function destroy(FuelIssue $fuelIssue)
    {
        $this->fuelService->deleteIssue($fuelIssue);

        return redirect()->route('fuel.issues.index')
            ->with('success', 'Fuel issue deleted and stock restored.');
    }
}
