<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::orderBy('name')->get();
        return view('settings.asset-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:asset_categories,name',
            'description' => 'nullable|string',
            'is_depreciable' => 'boolean',
            'default_depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'useful_life_years' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        AssetCategory::create($validated);
        return redirect()->back()->with('success', 'Asset category created.');
    }

    public function update(Request $request, AssetCategory $assetCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:asset_categories,name,' . $assetCategory->id,
            'description' => 'nullable|string',
            'is_depreciable' => 'boolean',
            'default_depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'useful_life_years' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $assetCategory->update($validated);
        return redirect()->back()->with('success', 'Asset category updated.');
    }

    public function destroy(AssetCategory $assetCategory)
    {
        $assetCategory->delete();
        return redirect()->back()->with('success', 'Asset category deleted.');
    }
}
