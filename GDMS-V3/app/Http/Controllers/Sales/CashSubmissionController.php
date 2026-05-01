<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\CashSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashSubmissionController extends Controller
{
    public function index()
    {
        $submissions = CashSubmission::with('sale.outlet')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('sales.cash.index', compact('submissions'));
    }

    public function create(Sale $sale)
    {
        return view('sales.cash.create', compact('sale'));
    }

    public function store(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'submitted_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'receipt_image' => 'nullable|file|image',
        ]);

        $submission = DB::transaction(function () use ($request, $sale, $validated) {
            $receiptPath = null;
            if ($request->hasFile('receipt_image')) {
                $receiptPath = $request->file('receipt_image')->store('receipts', 'public');
            }

            return CashSubmission::create([
                'reference' => 'CS-' . date('Ymd') . '-' . str_pad(CashSubmission::count() + 1, 4, '0', STR_PAD_LEFT),
                'sale_id' => $sale->id,
                'expected_amount' => $sale->total_price,
                'submitted_amount' => $validated['submitted_amount'],
                'notes' => $validated['notes'] ?? null,
                'receipt_image' => $receiptPath,
            ]);
        });

        return redirect()->route('cash-submissions.index')
            ->with('success', 'Cash submission recorded.');
    }

    public function show(CashSubmission $cashSubmission)
    {
        $cashSubmission->load('sale.outlet', 'sale.items.cylinderType');
        return view('sales.cash.show', compact('cashSubmission'));
    }
}