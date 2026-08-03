<?php

namespace App\Http\Controllers\Settings;

use App\Helpers\ReferenceGenerator;
use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\CompanyAsset;
use App\Models\Employee;
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
        $availableEmployees = Employee::where('status', 'active')->whereNull('outlet_id')->orderBy('first_name')->get();

        return view('settings.outlets.create', compact('availableAssets', 'availableEmployees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:outlets,name',
            'type' => 'required|in:car,physical',
            'location' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'opened_date' => 'required|date',
            'asset_id' => 'nullable|exists:assets,id',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'purchase_cost' => 'nullable|numeric|min:0',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['location'] = $validated['location'] ?? '';

        $employee = Employee::findOrFail($validated['employee_id']);
        if ($employee->outlet_id !== null) {
            return back()->withInput()->with('error', "{$employee->full_name} is already assigned to another outlet.");
        }

        return DB::transaction(function () use ($validated, $employee) {
            $outlet = Outlet::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'location' => $validated['location'],
                'plate_number' => $validated['plate_number'] ?? null,
                'is_active' => $validated['is_active'],
                'opened_date' => $validated['opened_date'],
            ]);

            if ($validated['type'] === 'car') {
                $outlet->update(['asset_id' => $this->resolveCarAsset($outlet, $validated)->id]);
            }

            $employee->update(['outlet_id' => $outlet->id]);

            return redirect()->route('outlets.index')
                ->with('success', 'Outlet created successfully.');
        });
    }

    /**
     * Either link the car-outlet to an existing depreciable asset, or create a new one for it.
     * The outlet's assigned employee doubles as the vehicle's driver.
     *
     * Depreciation rate is entered directly per vehicle rather than picked from a category —
     * the category still exists underneath (assets require one), but it's a single
     * auto-provisioned "Vehicles" bucket the user never has to see or manage.
     */
    protected function resolveCarAsset(Outlet $outlet, array $data): CompanyAsset
    {
        if (! empty($data['asset_id'])) {
            $asset = CompanyAsset::findOrFail($data['asset_id']);
            $asset->update([
                'assigned_to_outlet' => $outlet->id,
                'assigned_to_employee' => $data['employee_id'],
            ]);

            return $asset;
        }

        $category = $this->resolveVehicleCategory();

        $purchaseCost = $data['purchase_cost'] ?? 0;
        $depreciationRate = $data['depreciation_rate'] ?? $category->default_depreciation_rate;

        return CompanyAsset::create([
            'asset_number' => ReferenceGenerator::generateAssetNumber(),
            'name' => $outlet->name,
            'asset_category_id' => $category->id,
            'plate_number' => $data['plate_number'] ?? null,
            'purchase_date' => now()->toDateString(),
            'purchase_cost' => $purchaseCost,
            'accumulated_depreciation' => 0,
            'current_book_value' => $purchaseCost,
            'depreciation_rate' => $depreciationRate,
            'status' => 'active',
            'assigned_to_outlet' => $outlet->id,
            'assigned_to_employee' => $data['employee_id'],
        ]);
    }

    /**
     * Reuse an existing depreciable "Vehicles" category if one is already set up right;
     * otherwise create one. Never silently reuses a same-named category that isn't
     * actually marked depreciable — that would attach new vehicles to something that
     * never depreciates, and the business may have their own non-depreciable "Vehicles"
     * bucket for unrelated reasons.
     */
    protected function resolveVehicleCategory(): AssetCategory
    {
        $category = AssetCategory::whereIn('name', ['Vehicles', 'Vehicles (Depreciable)'])
            ->where('is_depreciable', true)
            ->orderBy('id')
            ->first();

        if ($category) {
            return $category;
        }

        $name = AssetCategory::where('name', 'Vehicles')->exists() ? 'Vehicles (Depreciable)' : 'Vehicles';

        return AssetCategory::create([
            'name' => $name,
            'is_depreciable' => true,
            'default_depreciation_rate' => 20,
            'is_active' => true,
        ]);
    }

    public function edit(Outlet $outlet)
    {
        $outlet->load('employee');
        $availableEmployees = Employee::where('status', 'active')
            ->where(function ($q) use ($outlet) {
                $q->whereNull('outlet_id')->orWhere('outlet_id', $outlet->id);
            })
            ->orderBy('first_name')
            ->get();

        return view('settings.outlets.edit', compact('outlet', 'availableEmployees'));
    }

    public function update(Request $request, Outlet $outlet)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:outlets,name,' . $outlet->id,
            'type' => 'required|in:car,physical',
            'location' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'opened_date' => 'required|date',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['location'] = $validated['location'] ?? '';

        $employee = Employee::findOrFail($validated['employee_id']);
        if ($employee->outlet_id !== null && $employee->outlet_id !== $outlet->id) {
            return back()->withInput()->with('error', "{$employee->full_name} is already assigned to another outlet.");
        }

        return DB::transaction(function () use ($validated, $outlet, $employee) {
            $outlet->update(collect($validated)->except('employee_id')->toArray());

            // Unassign whoever else was on this outlet, then assign the chosen employee.
            Employee::where('outlet_id', $outlet->id)->where('id', '!=', $employee->id)->update(['outlet_id' => null]);
            $employee->update(['outlet_id' => $outlet->id]);

            // Keep a car outlet's vehicle "driver" in sync with its assigned employee.
            if ($outlet->type === 'car' && $outlet->asset) {
                $outlet->asset->update(['assigned_to_employee' => $employee->id]);
            }

            return redirect()->route('outlets.index')
                ->with('success', 'Outlet updated successfully.');
        });
    }

    public function toggle(Outlet $outlet)
    {
        $outlet->update(['is_active' => ! $outlet->is_active]);

        return back()->with('success', 'Status toggled successfully.');
    }
}
