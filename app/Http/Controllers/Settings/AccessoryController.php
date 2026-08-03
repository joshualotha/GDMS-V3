<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccessoryRequest;
use App\Models\Accessory;
use Illuminate\Http\Request;

class AccessoryController extends Controller
{
    public function index()
    {
        $accessories = Accessory::orderBy('name')->get();
        return view('settings.accessories.index', compact('accessories'));
    }

    public function create()
    {
        return view('settings.accessories.create');
    }

    public function store(StoreAccessoryRequest $request)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active');

        Accessory::create($validated);

        return redirect()->route('accessories.index')
            ->with('success', 'Accessory created successfully.');
    }

    public function edit(Accessory $accessory)
    {
        return view('settings.accessories.edit', compact('accessory'));
    }

    public function update(StoreAccessoryRequest $request, Accessory $accessory)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active');

        $accessory->update($validated);

        return redirect()->route('accessories.index')
            ->with('success', 'Accessory updated successfully.');
    }

    public function toggle(Accessory $accessory)
    {
        $accessory->update(['is_active' => ! $accessory->is_active]);

        return back()->with('success', 'Status toggled successfully.');
    }
}
