<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCylinderTypeRequest;
use App\Models\CylinderType;
use App\Models\PriceHistory;
use Illuminate\Http\Request;

class CylinderTypeController extends Controller
{
    public function index()
    {
        $cylinderTypes = CylinderType::orderBy('size_kg')->get();
        return view('settings.cylinder-types.index', compact('cylinderTypes'));
    }

    public function create()
    {
        return view('settings.cylinder-types.create');
    }

    public function store(StoreCylinderTypeRequest $request)
    {
        $validated = $request->validated();
        $validated['is_active'] = $validated['is_active'] ?? true;

        CylinderType::create($validated);

        return redirect()->route('cylinder-types.index')
            ->with('success', 'Cylinder type created successfully.');
    }

    public function edit(CylinderType $cylinderType)
    {
        return view('settings.cylinder-types.edit', compact('cylinderType'));
    }

    public function update(StoreCylinderTypeRequest $request, CylinderType $cylinderType)
    {
        $validated = $request->validated();
        $validated['is_active'] = $validated['is_active'] ?? true;

        // Check if any price has changed - write to history
        $hasPriceChange =
            $cylinderType->full_sale_cost != $validated['full_sale_cost'] ||
            $cylinderType->full_sale_price != $validated['full_sale_price'] ||
            $cylinderType->refill_cost != $validated['refill_cost'] ||
            $cylinderType->refill_price != $validated['refill_price'];

        if ($hasPriceChange) {
            PriceHistory::create([
                'cylinder_type_id' => $cylinderType->id,
                'full_sale_cost' => $cylinderType->full_sale_cost,
                'full_sale_price' => $cylinderType->full_sale_price,
                'refill_cost' => $cylinderType->refill_cost,
                'refill_price' => $cylinderType->refill_price,
                'effective_from' => now(),
            ]);
        }

        $cylinderType->update($validated);

        return redirect()->route('cylinder-types.index')
            ->with('success', 'Cylinder type updated successfully.');
    }

    public function toggle(CylinderType $cylinderType)
    {
        $cylinderType->update(['is_active' => !$cylinderType->is_active]);

        return back()->with('success', 'Status toggled successfully.');
    }
}