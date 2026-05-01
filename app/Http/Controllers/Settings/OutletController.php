<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use App\Models\CompanyAsset;
use App\Models\FuelAsset;
use App\Models\Outlet;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function index()
    {
        $outlets = Outlet::orderBy('type')->orderBy('name')->get();

        return view('settings.outlets.index', compact('outlets'));
    }

    public function create()
    {
        $assets = CompanyAsset::orderBy('name')->get();

        return view('settings.outlets.create', compact('assets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:car,physical',
            'location' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'asset_id' => 'nullable|exists:assets,id',
            'purchase_cost' => 'nullable|numeric|min:0',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['location'] = $validated['location'] ?? '';

        $outlet = Outlet::create($validated);

        // If car type, also create/link asset
        if ($outlet->type === 'car' && $outlet->plate_number) {
            $this->syncAsset($outlet, $validated);
        }

        return redirect()->route('outlets.index')
            ->with('success', 'Outlet created successfully.');
    }

    protected function syncAsset(Outlet $outlet, array $data)
    {
        // Create FuelAsset for fuel tracking
        FuelAsset::firstOrCreate(
            ['plate_number' => $outlet->plate_number],
            ['name' => $outlet->name, 'type' => 'car', 'is_active' => true]
        );

        // Create or link CompanyAsset for asset register
        if (! empty($data['asset_id'])) {
            $asset = CompanyAsset::find($data['asset_id']);
            if ($asset) {
                $asset->update([
                    'assigned_to_outlet' => $outlet->id,
                    'plate_number' => $outlet->plate_number,
                ]);
            }
        } else {
            $category = AssetCategory::first();
            CompanyAsset::firstOrCreate(
                ['plate_number' => $outlet->plate_number],
                [
                    'name' => $outlet->name,
                    'asset_number' => 'AST-'.date('Y').'-'.str_pad(CompanyAsset::count() + 1, 4, '0', STR_PAD_LEFT),
                    'asset_category_id' => $category ? $category->id : 1,
                    'plate_number' => $outlet->plate_number,
                    'purchase_date' => now(),
                    'purchase_cost' => $data['purchase_cost'] ?? 0,
                    'accumulated_depreciation' => 0,
                    'current_book_value' => $data['purchase_cost'] ?? 0,
                    'status' => 'active',
                    'assigned_to_outlet' => $outlet->id,
                ]
            );
        }
    }

    public function edit(Outlet $outlet)
    {
        return view('settings.outlets.edit', compact('outlet'));
    }

    public function update(Request $request, Outlet $outlet)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:car,physical',
            'location' => 'required|string|max:255',
            'plate_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

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
