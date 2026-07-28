<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        // Most sales are recorded with cash already verified, so this list is just the
        // exceptions needing attention: nothing submitted yet, or flagged for follow-up.
        $pendingSales = Sale::whereIn('status', ['pending', 'queried'])
            ->with('outlet')
            ->orderBy('sale_date', 'desc')
            ->get();

        return view('sales.approvals.index', compact('pendingSales'));
    }

    public function approveSale(Sale $sale)
    {
        if (! in_array($sale->status, ['pending', 'queried'])) {
            return back()->with('error', 'Only pending or queried sales can be approved.');
        }

        if ($sale->cash_submitted === null) {
            return back()->with('error', 'Cash must be submitted before this sale can be approved.');
        }

        $sale->update(['status' => 'approved']);

        return back()->with('success', 'Sale approved and verified.');
    }

    public function querySale(Request $request, Sale $sale)
    {
        if (! in_array($sale->status, ['pending', 'approved'])) {
            return back()->with('error', 'This sale cannot be queried from its current status.');
        }

        $validated = $request->validate(['notes' => 'required|string']);
        $sale->update([
            'status' => 'queried',
            'notes' => $sale->notes."\n[Query]: ".$validated['notes'],
        ]);

        return back()->with('success', 'Sale queried.');
    }
}
