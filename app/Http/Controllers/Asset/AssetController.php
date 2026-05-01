<?php

namespace App\Http\Controllers\Asset;

use App\Helpers\ReferenceGenerator;
use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\CompanyAsset;
use App\Models\Outlet;
use App\Models\User;
use App\Services\DepreciationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $employees = User::orderBy('name')->get();
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
            'assigned_to_employee' => 'nullable|exists:users,id',
        ]);

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

        return redirect()->route('assets.index')
            ->with('success', 'Asset created successfully.');
    }

    public function show(CompanyAsset $asset)
    {
        $asset->load('category', 'outlet', 'employee', 'depreciationLogs', 'maintenanceLogs');

        return view('assets.show', compact('asset'));
    }

    public function edit(CompanyAsset $asset)
    {
        $categories = AssetCategory::orderBy('name')->get();
        $outlets = Outlet::orderBy('name')->get();
        $employees = User::orderBy('name')->get();

        return view('assets.edit', compact('asset', 'categories', 'outlets', 'employees'));
    }

    public function update(Request $request, CompanyAsset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'serial_number' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'required|numeric|min:0',
            'accumulated_depreciation' => 'nullable|numeric|min:0',
            'current_book_value' => 'nullable|numeric|min:0',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'assigned_to_outlet' => 'nullable|exists:outlets,id',
            'assigned_to_employee' => 'nullable|exists:users,id',
            'status' => 'required|in:active,disposed',
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
            'status' => $validated['status'],
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
            if (isset($validated['accumulated_depreciation'])) {
                $data['accumulated_depreciation'] = $validated['accumulated_depreciation'];
            }

            if (isset($validated['current_book_value'])) {
                $data['current_book_value'] = $validated['current_book_value'];
            } else {
                $data['current_book_value'] = ($validated['purchase_cost'] ?? 0) - ($validated['accumulated_depreciation'] ?? 0);
            }
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

        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    public function runDepreciation(Request $request)
    {
        $depreciationService = new DepreciationService;

        $assetId = $request->input('asset_id');
        $results = $depreciationService->runReducingBalance($assetId);

        if (empty($results)) {
            return redirect()->back()->with('info', 'No assets eligible for depreciation this month.');
        }

        $count = count($results);
        $total = array_sum(array_column($results, 'depreciation'));

        return redirect()->back()
            ->with('success', "Depreciation run for {$count} asset(s). Total: ".number_format($total, 2));
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
