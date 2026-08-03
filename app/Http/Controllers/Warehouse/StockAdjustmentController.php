<?php

namespace App\Http\Controllers\Warehouse;

use App\Helpers\ReferenceGenerator;
use App\Http\Controllers\Controller;
use App\Models\CylinderType;
use App\Models\Outlet;
use App\Models\StockAdjustment;
use App\Models\StockMain;
use App\Services\PayrollService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    protected $stockService;
    protected $payrollService;

    public function __construct(StockService $stockService, PayrollService $payrollService)
    {
        $this->stockService = $stockService;
        $this->payrollService = $payrollService;
    }

    public function index()
    {
        $adjustments = StockAdjustment::with('cylinderType', 'reversal', 'reverses', 'payrollItem.period', 'outlet.employee')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('warehouse.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        $cylinderTypes = CylinderType::where('is_active', true)->orderBy('size_kg')->get();
        $stockMain = StockMain::with('cylinderType')->get()->mapWithKeys(function ($item) {
            return [$item->cylinder_type_id => $item];
        });

        return view('warehouse.adjustments.create', compact('outlets', 'cylinderTypes', 'stockMain'));
    }

    public function store(Request $request)
    {
        $isMain = $request->input('is_main') == '1';

        $rules = [
            'is_main' => 'required|in:0,1',
            'adjustment_date' => 'required|date',
            'cylinder_type_id' => 'required|exists:cylinder_types,id',
            'type' => 'required|in:gain,loss,correction',
            'full_qty_change' => 'required|integer',
            'empty_qty_change' => 'required|integer',
            'reason' => 'required|string',
        ];

        if (! $isMain) {
            $rules['outlet_id'] = 'required|exists:outlets,id';
        }

        $validated = $request->validate($rules);

        $outletId = $isMain ? null : ($validated['outlet_id'] ?? null);

        return DB::transaction(function () use ($validated, $isMain, $outletId) {
            $adjustment = StockAdjustment::create([
                'adjustment_number' => ReferenceGenerator::generateAdjustmentNumber(),
                'outlet_id' => $outletId,
                'adjustment_date' => $validated['adjustment_date'],
                'is_main' => $isMain,
                'cylinder_type_id' => $validated['cylinder_type_id'],
                'type' => $validated['type'],
                'full_qty_change' => $validated['full_qty_change'],
                'empty_qty_change' => $validated['empty_qty_change'],
                'reason' => $validated['reason'],
            ]);

            $fullChange = $adjustment->appliedFullChange();
            $emptyChange = $adjustment->appliedEmptyChange();

            if ($isMain) {
                $this->stockService->updateMainStock(
                    $validated['cylinder_type_id'],
                    $fullChange,
                    $emptyChange,
                    'adjustment',
                    'StockAdjustment',
                    $adjustment->id,
                    "{$validated['type']}: {$validated['reason']}",
                    $validated['adjustment_date']
                );
            } else {
                $this->stockService->updateOutletStock(
                    $outletId,
                    $validated['cylinder_type_id'],
                    $fullChange,
                    $emptyChange,
                    'adjustment',
                    'StockAdjustment',
                    $adjustment->id,
                    "{$validated['type']}: {$validated['reason']}",
                    $validated['adjustment_date']
                );
            }

            $message = 'Stock adjustment posted successfully.';

            if ($adjustment->isOutletLoss()) {
                $employee = $adjustment->outlet->employee ?? null;

                if ($employee) {
                    $this->payrollService->applyOutletLossToPayroll($adjustment);
                    $adjustment->refresh();

                    $message .= $adjustment->payroll_item_id
                        ? ' '.number_format($adjustment->lossDeductionAmount(), 2)." deducted from {$employee->full_name}'s current payroll."
                        : " This will be deducted from {$employee->full_name}'s next payroll period.";
                }
            }

            return redirect()->route('stock-adjustments.index')
                ->with('success', $message);
        });
    }

    public function reverse(Request $request, StockAdjustment $adjustment)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        if ($adjustment->reversal()->exists()) {
            return back()->with('error', 'This adjustment has already been reversed.');
        }

        try {
            $refunded = true;

            DB::transaction(function () use ($adjustment, $validated, &$refunded) {
                $reversalFull = -$adjustment->appliedFullChange();
                $reversalEmpty = -$adjustment->appliedEmptyChange();

                $reversal = StockAdjustment::create([
                    'adjustment_number' => ReferenceGenerator::generateAdjustmentNumber(),
                    'reverses_adjustment_id' => $adjustment->id,
                    'outlet_id' => $adjustment->outlet_id,
                    'adjustment_date' => now()->toDateString(),
                    'is_main' => $adjustment->is_main,
                    'cylinder_type_id' => $adjustment->cylinder_type_id,
                    'type' => 'correction',
                    'full_qty_change' => $reversalFull,
                    'empty_qty_change' => $reversalEmpty,
                    'reason' => "Reversal of {$adjustment->adjustment_number}: {$validated['reason']}",
                ]);

                if ($adjustment->is_main) {
                    $this->stockService->updateMainStock(
                        $adjustment->cylinder_type_id,
                        $reversalFull,
                        $reversalEmpty,
                        'adjustment_reversal',
                        'StockAdjustment',
                        $reversal->id,
                        "Reversal of adjustment {$adjustment->adjustment_number}",
                        $reversal->adjustment_date
                    );
                } else {
                    $this->stockService->updateOutletStock(
                        $adjustment->outlet_id,
                        $adjustment->cylinder_type_id,
                        $reversalFull,
                        $reversalEmpty,
                        'adjustment_reversal',
                        'StockAdjustment',
                        $reversal->id,
                        "Reversal of adjustment {$adjustment->adjustment_number}",
                        $reversal->adjustment_date
                    );
                }

                if ($adjustment->isOutletLoss() && $adjustment->payroll_item_id) {
                    $refunded = $this->payrollService->refundReversedLoss($adjustment);
                }
            });

            $message = 'Adjustment reversed.';
            if (! $refunded) {
                $message .= ' Note: its payroll deduction was already on an approved/paid period, so it could not be refunded automatically — correct it manually if needed.';
            }

            return redirect()->route('stock-adjustments.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
