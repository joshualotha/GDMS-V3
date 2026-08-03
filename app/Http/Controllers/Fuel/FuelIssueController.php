<?php

namespace App\Http\Controllers\Fuel;

use App\Http\Controllers\Controller;
use App\Models\FuelIssue;
use App\Services\FuelService;

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

    public function destroy(FuelIssue $fuelIssue)
    {
        $this->fuelService->deleteIssue($fuelIssue);

        return redirect()->route('fuel.issues.index')
            ->with('success', 'Fuel issue deleted.');
    }
}
