<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function index()
    {
        $pendingSales = Sale::where('status', 'pending')
            ->with('outlet')
            ->get();

        return view('sales.approvals.index', compact('pendingSales'));
    }

    public function approveSale(Sale $sale)
    {
        $sale->update([
            'status' => 'approved',
            'cash_submitted' => $sale->total_price,
            'cash_submitted_date' => now()->toDateString(),
            'cash_variance' => 0,
        ]);

        return back()->with('success', 'Sale approved and verified.');
    }

    public function querySale(Request $request, Sale $sale)
    {
        $validated = $request->validate(['notes' => 'required|string']);
        $sale->update([
            'status' => 'queried',
            'notes' => $sale->notes."\n[Query]: ".$validated['notes'],
        ]);

        return back()->with('success', 'Sale queried.');
    }
}
