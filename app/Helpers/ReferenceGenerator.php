<?php

namespace App\Helpers;

use App\Models\PurchaseOrder;
use App\Models\GoodsReceived;
use App\Models\StockTransfer;
use App\Models\EmptyReturn;
use App\Models\Sale;
use App\Models\CashSubmission;
use App\Models\Expense;
use App\Models\CompanyAsset;
use App\Models\Employee;
use App\Models\StockAdjustment;

class ReferenceGenerator
{
    public static function generatePoNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "PO-{$year}";

        $lastPo = PurchaseOrder::where('po_number', 'like', "{$prefix}-%")
            ->orderBy('po_number', 'desc')
            ->first();

        $newNumber = $lastPo ? (int) substr($lastPo->po_number, -4) + 1 : 1;

        return "{$prefix}-" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function generateGrnNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "GRN-{$year}";

        $lastGrn = GoodsReceived::where('grn_number', 'like', "{$prefix}-%")
            ->orderBy('grn_number', 'desc')
            ->first();

        $newNumber = $lastGrn ? (int) substr($lastGrn->grn_number, -4) + 1 : 1;

        return "{$prefix}-" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function generateTransferNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "ST-{$year}";

        $lastTransfer = StockTransfer::where('transfer_number', 'like', "{$prefix}-%")
            ->orderBy('transfer_number', 'desc')
            ->first();

        $newNumber = $lastTransfer ? (int) substr($lastTransfer->transfer_number, -4) + 1 : 1;

        return "{$prefix}-" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function generateEmptyReturnNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "ER-{$year}";

        $lastReturn = EmptyReturn::where('return_number', 'like', "{$prefix}-%")
            ->orderBy('return_number', 'desc')
            ->first();

        $newNumber = $lastReturn ? (int) substr($lastReturn->return_number, -4) + 1 : 1;

        return "{$prefix}-" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function generateSaleNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "SL-{$year}";

        $lastSale = Sale::where('sale_number', 'like', "{$prefix}-%")
            ->orderBy('sale_number', 'desc')
            ->first();

        $newNumber = $lastSale ? (int) substr($lastSale->sale_number, -4) + 1 : 1;

        return "{$prefix}-" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function generateCashSubmissionNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "CS-{$year}";

        $lastSubmission = CashSubmission::where('submission_number', 'like', "{$prefix}-%")
            ->orderBy('submission_number', 'desc')
            ->first();

        $newNumber = $lastSubmission ? (int) substr($lastSubmission->submission_number, -4) + 1 : 1;

        return "{$prefix}-" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function generateExpenseNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "EXP-{$year}";

        $lastExpense = Expense::where('expense_number', 'like', "{$prefix}-%")
            ->orderBy('expense_number', 'desc')
            ->first();

        $newNumber = $lastExpense ? (int) substr($lastExpense->expense_number, -4) + 1 : 1;

        return "{$prefix}-" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function generateAssetNumber(): string
    {
        $prefix = "AST";

        $lastAsset = CompanyAsset::where('asset_number', 'like', "{$prefix}-%")
            ->orderBy('asset_number', 'desc')
            ->first();

        $newNumber = $lastAsset ? (int) substr($lastAsset->asset_number, -3) + 1 : 1;

        return "{$prefix}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public static function generateEmployeeNumber(): string
    {
        $prefix = "EMP";

        $lastEmployee = Employee::where('employee_number', 'like', "{$prefix}-%")
            ->orderBy('employee_number', 'desc')
            ->first();

        $newNumber = $lastEmployee ? (int) substr($lastEmployee->employee_number, -3) + 1 : 1;

        return "{$prefix}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public static function generateAdjustmentNumber(): string
    {
        $year = now()->format('Y');
        $prefix = "ADJ-{$year}";

        $lastAdjustment = StockAdjustment::where('adjustment_number', 'like', "{$prefix}-%")
            ->orderBy('adjustment_number', 'desc')
            ->first();

        $newNumber = $lastAdjustment ? (int) substr($lastAdjustment->adjustment_number, -4) + 1 : 1;

        return "{$prefix}-" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}