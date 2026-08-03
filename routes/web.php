<?php

use App\Http\Controllers\Asset\AssetController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\Fuel\FuelIssueController;
use App\Http\Controllers\Fuel\FuelPurchaseController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\PayrollItemController;
use App\Http\Controllers\HR\PayrollPeriodController;
use App\Http\Controllers\HR\PayslipController;
use App\Http\Controllers\Procurement\GoodsReceivedController;
use App\Http\Controllers\Procurement\PurchaseOrderController;
use App\Http\Controllers\Reports\AssetRegisterController;
use App\Http\Controllers\Reports\CashReconciliationController;
use App\Http\Controllers\Reports\DepreciationReportController;
use App\Http\Controllers\Reports\FuelReportController;
use App\Http\Controllers\Reports\PayrollReportController;
use App\Http\Controllers\Reports\ProcurementReportController;
use App\Http\Controllers\Reports\ProfitLossController;
use App\Http\Controllers\Reports\SalesReportController;
use App\Http\Controllers\Reports\StockMovementController;
use App\Http\Controllers\Reports\StockReportController;
use App\Http\Controllers\Sales\ApprovalController;
use App\Http\Controllers\Sales\OutletStockController;
use App\Http\Controllers\Sales\SaleController;
use App\Http\Controllers\Settings\AccessoryController;
use App\Http\Controllers\Settings\AssetCategoryController;
use App\Http\Controllers\Settings\CylinderTypeController;
use App\Http\Controllers\Settings\OutletController;
use App\Http\Controllers\Settings\SettingController;
use App\Http\Controllers\Settings\SupplierController;
use App\Http\Controllers\Warehouse\AccessoryPurchaseController;
use App\Http\Controllers\Warehouse\AccessoryStockController;
use App\Http\Controllers\Warehouse\AccessoryTransferController;
use App\Http\Controllers\Warehouse\OpeningStockController;
use App\Http\Controllers\Warehouse\ProcurementController;
use App\Http\Controllers\Warehouse\StockAdjustmentController;
use App\Http\Controllers\Warehouse\StockMainController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', fn () => redirect()->route('dashboard'));

    Route::get('/settings/cylinder-types', [CylinderTypeController::class, 'index'])->name('cylinder-types.index');
    Route::get('/settings/cylinder-types/create', [CylinderTypeController::class, 'create'])->name('cylinder-types.create');
    Route::post('/settings/cylinder-types', [CylinderTypeController::class, 'store'])->name('cylinder-types.store');
    Route::get('/settings/cylinder-types/{cylinder_type}/edit', [CylinderTypeController::class, 'edit'])->name('cylinder-types.edit');
    Route::put('/settings/cylinder-types/{cylinder_type}', [CylinderTypeController::class, 'update'])->name('cylinder-types.update');
    Route::post('/settings/cylinder-types/{cylinder_type}/toggle', [CylinderTypeController::class, 'toggle'])->name('cylinder-types.toggle');

    Route::get('/settings/accessories', [AccessoryController::class, 'index'])->name('accessories.index');
    Route::get('/settings/accessories/create', [AccessoryController::class, 'create'])->name('accessories.create');
    Route::post('/settings/accessories', [AccessoryController::class, 'store'])->name('accessories.store');
    Route::get('/settings/accessories/{accessory}/edit', [AccessoryController::class, 'edit'])->name('accessories.edit');
    Route::put('/settings/accessories/{accessory}', [AccessoryController::class, 'update'])->name('accessories.update');
    Route::post('/settings/accessories/{accessory}/toggle', [AccessoryController::class, 'toggle'])->name('accessories.toggle');

    Route::get('/settings/outlets', [OutletController::class, 'index'])->name('outlets.index');
    Route::get('/settings/outlets/create', [OutletController::class, 'create'])->name('outlets.create');
    Route::post('/settings/outlets', [OutletController::class, 'store'])->name('outlets.store');
    Route::get('/settings/outlets/{outlet}/edit', [OutletController::class, 'edit'])->name('outlets.edit');
    Route::put('/settings/outlets/{outlet}', [OutletController::class, 'update'])->name('outlets.update');
    Route::post('/settings/outlets/{outlet}/toggle', [OutletController::class, 'toggle'])->name('outlets.toggle');

    Route::get('/settings/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/settings/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/settings/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/settings/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/settings/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::post('/settings/suppliers/{supplier}/toggle', [SupplierController::class, 'toggle'])->name('suppliers.toggle');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::post('/customers/{customer}/toggle', [CustomerController::class, 'toggle'])->name('customers.toggle');

    Route::get('/settings/general', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/general', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/general/account', [SettingController::class, 'updateAccount'])->name('settings.update-account');
    Route::post('/settings/general/reset-all', [SettingController::class, 'resetAll'])->name('settings.reset-all');

    Route::get('/settings/asset-categories', [AssetCategoryController::class, 'index'])->name('asset-categories.index');
    Route::get('/settings/asset-categories/create', [AssetCategoryController::class, 'create'])->name('asset-categories.create');
    Route::post('/settings/asset-categories', [AssetCategoryController::class, 'store'])->name('asset-categories.store');
    Route::get('/settings/asset-categories/{asset_category}/edit', [AssetCategoryController::class, 'edit'])->name('asset-categories.edit');
    Route::put('/settings/asset-categories/{asset_category}', [AssetCategoryController::class, 'update'])->name('asset-categories.update');
    Route::post('/settings/asset-categories/{asset_category}/toggle', [AssetCategoryController::class, 'toggle'])->name('asset-categories.toggle');

    // NOTE: the formal Purchase Order -> Goods Received workflow (app/Http/Controllers/Procurement/*)
    // is intentionally unrouted. The business uses "Quick Receiving" (warehouse/procurement) as the
    // single procurement flow. The PO/GRN code is left in place in case it's needed again later.

    Route::get('/warehouse/opening-stock', [OpeningStockController::class, 'index'])->name('opening-stock.index');
    Route::get('/warehouse/opening-stock/create', [OpeningStockController::class, 'create'])->name('opening-stock.create');
    Route::post('/warehouse/opening-stock', [OpeningStockController::class, 'store'])->name('opening-stock.store');

    Route::get('/warehouse/stock-ledger', [StockMainController::class, 'index'])->name('stock-ledger.index');
    Route::get('/warehouse/procurement', [ProcurementController::class, 'index'])->name('warehouse.procurement');
    Route::get('/warehouse/procurement/create', [ProcurementController::class, 'create'])->name('warehouse.procurement.create');
    Route::post('/warehouse/procurement', [ProcurementController::class, 'store'])->name('warehouse.procurement.store');
    Route::post('/warehouse/procurement/{goodsReceived}/cancel', [ProcurementController::class, 'cancel'])->name('warehouse.procurement.cancel');

    Route::get('/warehouse/accessory-purchases', [AccessoryPurchaseController::class, 'index'])->name('accessory-purchases.index');
    Route::get('/warehouse/accessory-purchases/create', [AccessoryPurchaseController::class, 'create'])->name('accessory-purchases.create');
    Route::post('/warehouse/accessory-purchases', [AccessoryPurchaseController::class, 'store'])->name('accessory-purchases.store');
    Route::post('/warehouse/accessory-purchases/{accessoryPurchase}/cancel', [AccessoryPurchaseController::class, 'cancel'])->name('accessory-purchases.cancel');

    Route::get('/warehouse/accessory-transfers', [AccessoryTransferController::class, 'index'])->name('accessory-transfers.index');
    Route::get('/warehouse/accessory-transfers/create', [AccessoryTransferController::class, 'create'])->name('accessory-transfers.create');
    Route::post('/warehouse/accessory-transfers', [AccessoryTransferController::class, 'store'])->name('accessory-transfers.store');
    Route::get('/warehouse/accessory-transfers/stock/{outletId}', [AccessoryTransferController::class, 'getStock'])->name('accessory-transfers.stock');
    Route::post('/warehouse/accessory-transfers/{accessoryTransfer}/cancel', [AccessoryTransferController::class, 'cancel'])->name('accessory-transfers.cancel');

    Route::get('/warehouse/accessory-stock', [AccessoryStockController::class, 'index'])->name('accessory-stock.index');

    Route::get('/warehouse/movements', [App\Http\Controllers\Warehouse\StockMovementController::class, 'index'])->name('warehouse.movements');
    Route::get('/warehouse/movements/create', [App\Http\Controllers\Warehouse\StockMovementController::class, 'create'])->name('warehouse.movements.create');
    Route::post('/warehouse/movements', [App\Http\Controllers\Warehouse\StockMovementController::class, 'store'])->name('warehouse.movements.store');
    Route::get('/warehouse/movements/stock/{outletId}', [App\Http\Controllers\Warehouse\StockMovementController::class, 'getStock'])->name('warehouse.movements.stock');
    Route::post('/warehouse/movements/transfers/{stockTransfer}/cancel', [App\Http\Controllers\Warehouse\StockMovementController::class, 'cancelTransfer'])->name('warehouse.movements.cancelTransfer');
    Route::post('/warehouse/movements/returns/{emptyReturn}/cancel', [App\Http\Controllers\Warehouse\StockMovementController::class, 'cancelReturn'])->name('warehouse.movements.cancelReturn');

    Route::get('/warehouse/adjustments', [StockAdjustmentController::class, 'index'])->name('stock-adjustments.index');
    Route::get('/warehouse/adjustments/create', [StockAdjustmentController::class, 'create'])->name('stock-adjustments.create');
    Route::post('/warehouse/adjustments', [StockAdjustmentController::class, 'store'])->name('stock-adjustments.store');
    Route::post('/warehouse/adjustments/{adjustment}/reverse', [StockAdjustmentController::class, 'reverse'])->name('stock-adjustments.reverse');

    Route::get('/sales/outlet-stock', [OutletStockController::class, 'index'])->name('outlet-stock.index');
    Route::get('/api/outlet/{outlet}/stock', [SaleController::class, 'getOutletStock']);
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::post('/sales/{sale}/submit-cash', [SaleController::class, 'submitCash'])->name('sales.submitCash');
    Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');

    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/sales/{sale}/approve', [ApprovalController::class, 'approveSale'])->name('approvals.sales.approve');
    Route::post('/approvals/sales/{sale}/query', [ApprovalController::class, 'querySale'])->name('approvals.sales.query');

    // NOTE: fuel is now bought at the pump for one specific vehicle at a time (cash, litres +
    // odometer recorded on the spot) — see fuel/purchases. The old bulk warehouse-pool workflow
    // (buy into FuelStock, then separately "issue" litres to a vehicle) is retired going forward;
    // FuelIssueController/its create+store routes are left unrouted, existing records stay as history.
    Route::get('/fuel/stock', [FuelPurchaseController::class, 'stock'])->name('fuel.stock');
    Route::get('/fuel/purchases', [FuelPurchaseController::class, 'index'])->name('fuel.purchases.index');
    Route::get('/fuel/purchases/create', [FuelPurchaseController::class, 'create'])->name('fuel.purchases.create');
    Route::post('/fuel/purchases', [FuelPurchaseController::class, 'store'])->name('fuel.purchases.store');
    Route::delete('/fuel/purchases/{fuelPurchase}', [FuelPurchaseController::class, 'destroy'])->name('fuel.purchases.destroy');
    Route::get('/fuel/issues', [FuelIssueController::class, 'index'])->name('fuel.issues.index');
    Route::delete('/fuel/issues/{fuelIssue}', [FuelIssueController::class, 'destroy'])->name('fuel.issues.destroy');

    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    Route::post('/assets/{asset}/dispose', [AssetController::class, 'dispose'])->name('assets.dispose');
    Route::post('/assets/{asset}/reactivate', [AssetController::class, 'reactivate'])->name('assets.reactivate');
    Route::post('/assets/catch-up-depreciation', [AssetController::class, 'catchUpDepreciation'])->name('assets.catch-up-depreciation');

    Route::get('/hr/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/hr/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/hr/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/hr/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/hr/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/hr/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/hr/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    Route::get('/payroll', [PayrollPeriodController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/create', [PayrollPeriodController::class, 'create'])->name('payroll.create');
    Route::post('/payroll', [PayrollPeriodController::class, 'store'])->name('payroll.store');
    Route::get('/payroll/{period}', [PayrollPeriodController::class, 'show'])->name('payroll.show');
    Route::post('/payroll/{period}/approve', [PayrollPeriodController::class, 'approve'])->name('payroll.approve');
    Route::post('/payroll/{period}/unapprove', [PayrollPeriodController::class, 'unapprove'])->name('payroll.unapprove');
    Route::post('/payroll/{period}/mark-paid', [PayrollPeriodController::class, 'markPaid'])->name('payroll.markPaid');
    Route::post('/payroll/{period}/add-employee', [PayrollPeriodController::class, 'addEmployee'])->name('payroll.addEmployee');
    Route::delete('/payroll/{period}', [PayrollPeriodController::class, 'destroy'])->name('payroll.destroy');
    Route::put('/payroll/item/{item}', [PayrollItemController::class, 'update'])->name('payroll.item.update');
    Route::delete('/payroll/item/{item}', [PayrollItemController::class, 'destroy'])->name('payroll.item.destroy');
    Route::get('/payroll/item/{item}/payslip', [PayslipController::class, 'show'])->name('payslip.show');
    Route::get('/payroll/item/{item}/payslip/download', [PayslipController::class, 'download'])->name('payslip.download');

    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/settings/expense-categories', [ExpenseCategoryController::class, 'index'])->name('expense-categories.index');
    Route::get('/settings/expense-categories/create', [ExpenseCategoryController::class, 'create'])->name('expense-categories.create');
    Route::post('/settings/expense-categories', [ExpenseCategoryController::class, 'store'])->name('expense-categories.store');
    Route::get('/settings/expense-categories/{expense_category}/edit', [ExpenseCategoryController::class, 'edit'])->name('expense-categories.edit');
    Route::put('/settings/expense-categories/{expense_category}', [ExpenseCategoryController::class, 'update'])->name('expense-categories.update');
    Route::post('/settings/expense-categories/{expense_category}/toggle', [ExpenseCategoryController::class, 'toggle'])->name('expense-categories.toggle');

    Route::get('/reports/stock', [StockReportController::class, 'index'])->name('reports.stock.index');
    Route::get('/reports/stock/export', [StockReportController::class, 'export'])->name('reports.stock.export');
    Route::get('/reports/stock-movement', [StockMovementController::class, 'index'])->name('reports.stock-movement.index');
    Route::get('/reports/stock-movement/export', [StockMovementController::class, 'export'])->name('reports.stock-movement.export');
    Route::get('/reports/sales', [SalesReportController::class, 'index'])->name('reports.sales.index');
    Route::get('/reports/sales/export', [SalesReportController::class, 'export'])->name('reports.sales.export');
    Route::get('/reports/procurement', [ProcurementReportController::class, 'index'])->name('reports.procurement.index');
    Route::get('/reports/procurement/export', [ProcurementReportController::class, 'export'])->name('reports.procurement.export');
    Route::get('/reports/cash', [CashReconciliationController::class, 'index'])->name('reports.cash.index');
    Route::get('/reports/cash/export', [CashReconciliationController::class, 'export'])->name('reports.cash.export');
    Route::get('/reports/fuel', [FuelReportController::class, 'index'])->name('reports.fuel.index');
    Route::get('/reports/fuel/export', [FuelReportController::class, 'export'])->name('reports.fuel.export');
    Route::get('/reports/asset', [AssetRegisterController::class, 'index'])->name('reports.asset.index');
    Route::get('/reports/asset/export', [AssetRegisterController::class, 'export'])->name('reports.asset.export');
    Route::get('/reports/depreciation', [DepreciationReportController::class, 'index'])->name('reports.depreciation.index');
    Route::get('/reports/depreciation/export', [DepreciationReportController::class, 'export'])->name('reports.depreciation.export');
    Route::get('/reports/payroll', [PayrollReportController::class, 'index'])->name('reports.payroll.index');
    Route::get('/reports/payroll/export', [PayrollReportController::class, 'export'])->name('reports.payroll.export');
    Route::get('/reports/profit-loss', [ProfitLossController::class, 'index'])->name('reports.profit-loss.index');
    Route::get('/reports/profit-loss/export', [ProfitLossController::class, 'export'])->name('reports.profit-loss.export');
});
