<?php

namespace App\Http\Controllers\Asset;

use App\Helpers\ReferenceGenerator;
use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\CompanyAsset;
use App\Models\Employee;
use App\Models\Outlet;
use App\Services\DepreciationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    public function index()
    {
        $assets = CompanyAsset::with('category', 'outlet', 'employee')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        $categories = AssetCategory::orderBy('name')->get();
        $outlets = Outlet::orderBy('name')->get();
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();
        $categoryWarning = $categories->isEmpty() ? 'Please create at least one Asset Category in Settings before adding assets.' : null;

        return view('assets.create', compact('categories', 'outlets', 'employees', 'categoryWarning'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'serial_number' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'required|numeric|min:0',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'assigned_to_outlet' => 'nullable|exists:outlets,id',
            'assigned_to_employee' => 'nullable|exists:employees,id',
        ]);

        DB::transaction(function () use ($validated) {
            $assetNumber = ReferenceGenerator::generateAssetNumber();

            $category = AssetCategory::find($validated['asset_category_id']);
            $depreciationRate = $validated['depreciation_rate'] ?? $category->default_depreciation_rate;

            $purchaseCost = $validated['purchase_cost'];
            $purchaseDate = $validated['purchase_date'] ?? now()->toDateString();

            // Create the asset with initial values (book value = purchase cost)
            $asset = CompanyAsset::create([
                'asset_number' => $assetNumber,
                'name' => $validated['name'],
                'asset_category_id' => $validated['asset_category_id'],
                'serial_number' => $validated['serial_number'] ?? null,
                'plate_number' => $validated['plate_number'] ?? null,
                'purchase_date' => $purchaseDate,
                'purchase_cost' => $purchaseCost,
                'accumulated_depreciation' => 0,
                'current_book_value' => $purchaseCost,
                'depreciation_rate' => $depreciationRate,
                'assigned_to_outlet' => $validated['assigned_to_outlet'] ?? null,
                'assigned_to_employee' => $validated['assigned_to_employee'] ?? null,
                'status' => 'active',
            ]);

            // Auto-calculate past depreciation from purchase date using the service
            if ($depreciationRate > 0 && $purchaseDate) {
                $depreciationService = new DepreciationService();
                $depreciationService->catchUpDepreciation($asset->id);
            }
        });

        return redirect()->route('assets.index')
            ->with('success', 'Asset created successfully.');
    }

    public function show(CompanyAsset $asset)
    {
        $asset->load('category', 'outlet', 'employee', 'depreciationLogs', 'expenses', 'outletAsCar');

        return view('assets.show', compact('asset'));
    }

    public function edit(CompanyAsset $asset)
    {
        $categories = AssetCategory::orderBy('name')->get();
        $outlets = Outlet::orderBy('name')->get();
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('assets.edit', compact('asset', 'categories', 'outlets', 'employees'));
    }

    public function update(Request $request, CompanyAsset $asset)
    {
        if ($asset->status === 'disposed') {
            return redirect()->route('assets.show', $asset)
                ->with('error', 'This asset is disposed. Reactivate it before editing.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'serial_number' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'required|numeric|min:0',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'assigned_to_outlet' => 'nullable|exists:outlets,id',
            'assigned_to_employee' => 'nullable|exists:employees,id',
        ]);

        $data = [
            'name' => $validated['name'],
            'asset_category_id' => $validated['asset_category_id'],
            'serial_number' => $validated['serial_number'] ?? null,
            'plate_number' => $validated['plate_number'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'purchase_cost' => $validated['purchase_cost'],
            'depreciation_rate' => $validated['depreciation_rate'] ?? 0,
            'assigned_to_outlet' => $validated['assigned_to_outlet'] ?? null,
            'assigned_to_employee' => $validated['assigned_to_employee'] ?? null,
        ];

        // Check if depreciation-affecting fields changed
        $rateChanged = ($validated['depreciation_rate'] ?? 0) != $asset->depreciation_rate;
        $costChanged = $validated['purchase_cost'] != $asset->purchase_cost;
        $dateChanged = ($validated['purchase_date'] ?? null) != ($asset->purchase_date?->toDateString());

        if ($rateChanged || $costChanged || $dateChanged) {
            // Reset to initial values and let catchUpDepreciation recalculate
            $data['accumulated_depreciation'] = 0;
            $data['current_book_value'] = $validated['purchase_cost'];

            // Clear old depreciation logs so catchUp can recreate them
            $asset->depreciationLogs()->delete();
        } else {
            // accumulated_depreciation and current_book_value are system-computed
            // (display-only in the edit form) — leave them untouched here.
        }

        $asset->update($data);

        // Recalculate depreciation if rate/cost/date changed
        if ($rateChanged || $costChanged || $dateChanged) {
            $depreciationService = new DepreciationService();
            $depreciationService->catchUpDepreciation($asset->id);
        }

        return redirect()->route('assets.index')
            ->with('success', 'Asset updated successfully.');
    }

    public function destroy(CompanyAsset $asset)
    {
        if ($asset->status === 'disposed') {
            return redirect()->route('assets.index')
                ->with('error', 'Cannot delete a disposed asset.');
        }

        if ($asset->outletAsCar()->exists()) {
            return redirect()->route('assets.index')
                ->with('error', 'Cannot delete this asset — it is the vehicle for outlet "' . $asset->outletAsCar->name . '". Delete or re-link that outlet first.');
        }

        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    /**
     * Disposal is a dedicated action, not a status dropdown in the general edit form.
     * It intentionally leaves purchase cost, depreciation rate, accumulated depreciation,
     * and book value exactly as they were — a disposal shouldn't rewrite history.
     */
    public function dispose(Request $request, CompanyAsset $asset)
    {
        if ($asset->status === 'disposed') {
            return back()->with('error', 'This asset is already disposed.');
        }

        $validated = $request->validate([
            'disposed_at' => 'required|date',
            'disposal_notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($asset, $validated) {
            $asset->update([
                'status' => 'disposed',
                'disposed_at' => $validated['disposed_at'],
                'disposal_notes' => $validated['disposal_notes'] ?? null,
            ]);

            // A disposed vehicle can't keep selling as an outlet.
            if ($asset->outletAsCar) {
                $asset->outletAsCar->update(['is_active' => false]);
            }
        });

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset marked as disposed.');
    }

    public function reactivate(CompanyAsset $asset)
    {
        if ($asset->status !== 'disposed') {
            return back()->with('error', 'This asset is not disposed.');
        }

        $asset->update([
            'status' => 'active',
            'disposed_at' => null,
            'disposal_notes' => null,
        ]);

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset reactivated. Note: its outlet (if any) was deactivated on disposal and was not automatically reactivated.');
    }

    public function catchUpDepreciation(Request $request)
    {
        $depreciationService = new DepreciationService;

        $assetId = $request->input('asset_id');
        $results = $depreciationService->catchUpDepreciation($assetId);

        if (empty($results)) {
            return redirect()->back()->with('info', 'All assets are already up to date.');
        }

        $count = count($results);
        $totalMonths = array_sum(array_column($results, 'months_caught_up'));

        return redirect()->back()
            ->with('success', "Caught up depreciation for {$count} asset(s) ({$totalMonths} total months).");
    }
}
