<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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

        $admin = Auth::user();

        return view('settings.general.index', compact('settings', 'admin'));
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

    public function updateAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $request->validate([
                'current_password' => 'required|string|current_password',
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Account updated successfully.');
    }

    /**
     * TEMPORARY TESTING UTILITY — wipes every business-data table but leaves the
     * `users` table (and Laravel's own infra tables) untouched, so the admin stays
     * logged in and able to keep testing on a clean slate. Remove this before go-live.
     */
    public function resetAll(Request $request)
    {
        if (app()->environment('production')) {
            abort(403, 'Reset is disabled in production.');
        }

        $request->validate([
            'confirm' => 'required|in:RESET',
        ]);

        $preserve = [
            'users',
            'migrations',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'failed_jobs',
            'job_batches',
        ];

        $tables = array_diff(Schema::getTableListing(), $preserve);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return redirect()->route('settings.index')
            ->with('success', 'All data reset. The admin account was left untouched.');
    }
}