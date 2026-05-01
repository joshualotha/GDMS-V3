<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'business_name' => Setting::get('business_name', ''),
            'address' => Setting::get('address', ''),
            'currency' => Setting::get('currency', 'KES'),
            'financial_year_start' => Setting::get('financial_year_start', '01'),
        ];

        return view('settings.general.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'currency' => 'required|string|max:10',
            'financial_year_start' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}