<?php

namespace App\Helpers;

use App\Models\PurchaseOrder;
use App\Models\GoodsReceived;
use App\Models\StockTransfer;
use App\Models\EmptyReturn;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\CompanyAsset;
use App\Models\Employee;
use App\Models\StockAdjustment;
use App\Models\AccessoryPurchase;
use App\Models\AccessoryTransfer;

class ReferenceGenerator
{
    /**
     * Compute the next zero-padded reference number for a prefix.
     * Locks matching rows and computes the numeric max via SQL (not string sort),
     * so it stays correct once the suffix grows past its original digit count
     * (e.g. EMP-999 -> EMP-1000, where a plain string ORDER BY DESC breaks).
     */
    private static function nextNumber(string $modelClass, string $column, string $prefix, int $padLength): string
    {
        $startPos = strlen($prefix) + 2; // 1-indexed SQL position, skips "{prefix}-"

        $max = $modelClass::where($column, 'like', "{$prefix}-%")
            ->lockForUpdate()
            ->selectRaw("MAX(CAST(SUBSTRING({$column}, {$startPos}) AS UNSIGNED)) as max_num")
            ->value('max_num');

        $newNumber = ($max ?? 0) + 1;

        return "{$prefix}-" . str_pad($newNumber, $padLength, '0', STR_PAD_LEFT);
    }

    public static function generatePoNumber(): string
    {
        return self::nextNumber(PurchaseOrder::class, 'po_number', 'PO-' . now()->format('Y'), 4);
    }

    public static function generateGrnNumber(): string
    {
        return self::nextNumber(GoodsReceived::class, 'grn_number', 'GRN-' . now()->format('Y'), 4);
    }

    public static function generateTransferNumber(): string
    {
        return self::nextNumber(StockTransfer::class, 'transfer_number', 'ST-' . now()->format('Y'), 4);
    }

    public static function generateEmptyReturnNumber(): string
    {
        return self::nextNumber(EmptyReturn::class, 'return_number', 'ER-' . now()->format('Y'), 4);
    }

    public static function generateSaleNumber(): string
    {
        return self::nextNumber(Sale::class, 'sale_number', 'SL-' . now()->format('Y'), 4);
    }

    public static function generateExpenseNumber(): string
    {
        return self::nextNumber(Expense::class, 'expense_number', 'EXP-' . now()->format('Y'), 4);
    }

    public static function generateAssetNumber(): string
    {
        return self::nextNumber(CompanyAsset::class, 'asset_number', 'AST', 3);
    }

    public static function generateEmployeeNumber(): string
    {
        return self::nextNumber(Employee::class, 'employee_number', 'EMP', 3);
    }

    public static function generateAdjustmentNumber(): string
    {
        return self::nextNumber(StockAdjustment::class, 'adjustment_number', 'ADJ-' . now()->format('Y'), 4);
    }

    public static function generateAccessoryPurchaseNumber(): string
    {
        return self::nextNumber(AccessoryPurchase::class, 'purchase_number', 'ACP-' . now()->format('Y'), 4);
    }

    public static function generateAccessoryTransferNumber(): string
    {
        return self::nextNumber(AccessoryTransfer::class, 'transfer_number', 'ATR-' . now()->format('Y'), 4);
    }
}
