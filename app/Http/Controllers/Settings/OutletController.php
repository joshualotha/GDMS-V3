<?php

namespace App\Http\Controllers\Settings;

use App\Helpers\ReferenceGenerator;
use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\CompanyAsset;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutletController extends Controller
{
    public function index()
    {
        $outlets = Outlet::with('asset')->orderBy('type')->orderBy('name')->get();

        return view('settings.outlets.index', compact('outlets'));
    }

    public function create()
    {
        // Cars are depreciable assets, so only offer depreciable-category assets
        // that aren't already some other outlet's vehicle.
        $availableAssets = CompanyAsset::whereHas('category', fn ($q) => $q->where('is_depreciable', true))
            ->whereDoesntHave('outletAsCar')
            ->orderBy('name')
            ->get();
        $depreciableCategories = AssetCategory::where('is_depreciable', true)->where('is_active', true)->orderBy('name')->get();

        return view('settings.outlets.create', compact('availableAssets', 'depreciableCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:outlets,name',
            'type' => 'required|in:car,physical',
            'location' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'asset_id' => 'nullable|exists:assets,id',
            'asset_category_id' => 'required_if:type,car|nullable|exists:asset_categories,id',
            'purchase_cost' => 'nullable|numeric|min:0',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['location'] = $validated['location'] ?? '';

        return DB::transaction(function () use ($validated) {
            $outlet = Outlet::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'location' => $validated['location'],
                'plate_number' => $validated['plate_number'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            if ($validated['type'] === 'car') {
                $outlet->update(['asset_id' => $this->resolveCarAsset($outlet, $validated)->id]);
            }

            return redirect()->route('outlets.index')
                ->with('success', 'Outlet created successfully.');
        });
    }

    /**
     * Either link the car-outlet to an existing depreciable asset, or create a new one for it.
     */
    protected function resolveCarAsset(Outlet $outlet, array $data): CompanyAsset
    {
        if (! empty($data['asset_id'])) {
            $asset = CompanyAsset::findOrFail($data['asset_id']);
            $asset->update(['assigned_to_outlet' => $outlet->id]);

            return $asset;
        }

        $category = AssetCategory::findOrFail($data['asset_category_id']);
        $purchaseCost = $data['purchase_cost'] ?? 0;

        return CompanyAsset::create([
            'asset_number' => ReferenceGenerator::generateAssetNumber(),
            'name' => $outlet->name,
            'asset_category_id' => $category->id,
            'plate_number' => $data['plate_number'] ?? null,
            'purchase_date' => now()->toDateString(),
            'purchase_cost' => $purchaseCost,
            'accumulated_depreciation' => 0,
            'current_book_value' => $purchaseCost,
            'depreciation_rate' => $category->default_depreciation_rate,
            'status' => 'active',
            'assigned_to_outlet' => $outlet->id,
        ]);
    }

    public function edit(Outlet $outlet)
    {
        return view('settings.outlets.edit', compact('outlet'));
    }

    public function update(Request $request, Outlet $outlet)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:outlets,name,' . $outlet->id,
            'type' => 'required|in:car,physical',
            'location' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['location'] = $validated['location'] ?? '';

        $outlet->update($validated);

        return redirect()->route('outlets.index')
            ->with('success', 'Outlet updated successfully.');
    }

    public function toggle(Outlet $outlet)
    {
        $outlet->update(['is_active' => ! $outlet->is_active]);

        return back()->with('success', 'Status toggled successfully.');
    }
}
