@extends('layouts.app')

@section('title', 'General Settings')

@section('header', 'General Settings')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- System Settings --}}
        <form action="{{ route('settings.update') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf

            <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">System Settings</h3>
                    <p class="text-sm text-gray-500">Configure your business information</p>
                </div>
            </div>

            <div>
                <label for="business_name" class="block text-sm font-medium text-gray-700">Business Name</label>
                <input type="text" name="business_name" id="business_name" value="{{ old('business_name', $settings['business_name']) }}" required
                    class="mt-1 form-input">
                @error('business_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" id="address" rows="3"
                    class="mt-1 form-input">{{ old('address', $settings['address']) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                    <input type="text" name="currency" id="currency" value="{{ old('currency', $settings['currency']) }}" required
                        class="mt-1 form-input">
                    @error('currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="financial_year_start" class="block text-sm font-medium text-gray-700">Financial Year Start</label>
                    <select name="financial_year_start" id="financial_year_start" required
                        class="mt-1 form-select">
                        <option value="01" {{ $settings['financial_year_start'] == '01' ? 'selected' : '' }}>January</option>
                        <option value="02" {{ $settings['financial_year_start'] == '02' ? 'selected' : '' }}>February</option>
                        <option value="03" {{ $settings['financial_year_start'] == '03' ? 'selected' : '' }}>March</option>
                        <option value="04" {{ $settings['financial_year_start'] == '04' ? 'selected' : '' }}>April</option>
                        <option value="05" {{ $settings['financial_year_start'] == '05' ? 'selected' : '' }}>May</option>
                        <option value="06" {{ $settings['financial_year_start'] == '06' ? 'selected' : '' }}>June</option>
                        <option value="07" {{ $settings['financial_year_start'] == '07' ? 'selected' : '' }}>July</option>
                        <option value="08" {{ $settings['financial_year_start'] == '08' ? 'selected' : '' }}>August</option>
                        <option value="09" {{ $settings['financial_year_start'] == '09' ? 'selected' : '' }}>September</option>
                        <option value="10" {{ $settings['financial_year_start'] == '10' ? 'selected' : '' }}>October</option>
                        <option value="11" {{ $settings['financial_year_start'] == '11' ? 'selected' : '' }}>November</option>
                        <option value="12" {{ $settings['financial_year_start'] == '12' ? 'selected' : '' }}>December</option>
                    </select>
                    @error('financial_year_start')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Settings
                </button>
            </div>
        </form>

        {{-- Admin Account --}}
        <form action="{{ route('settings.update-account') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf

            <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Admin Account</h3>
                    <p class="text-sm text-gray-500">Manage your profile and login credentials</p>
                </div>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" required
                    class="mt-1 form-input">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}" required
                    class="mt-1 form-input">
                <p class="mt-1 text-xs text-gray-500">Used to receive password reset links. Enter this email on the forgot password page.</p>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t border-gray-100 pt-4">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Change Password</span>
                    <span class="text-xs text-gray-400">(optional)</span>
                </div>

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" name="current_password" id="current_password"
                        class="mt-1 form-input">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" id="password"
                            class="mt-1 form-input">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="mt-1 form-input">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-5 py-2.5 rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Account
                </button>
            </div>
        </form>
    </div>

    {{-- Danger Zone: TEMPORARY testing utility, remove before go-live --}}
    <div class="bg-white rounded-lg shadow-sm border border-red-200 p-6">
        <div class="flex items-center gap-3 border-b border-red-100 pb-4 mb-4">
            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Danger Zone</h3>
                <p class="text-sm text-gray-500">Testing utility only &mdash; will be removed before go-live.</p>
            </div>
        </div>

        <p class="text-sm text-gray-600 mb-4">
            Permanently deletes every record in the system &mdash; sales, stock, outlets, employees, payroll, everything &mdash;
            except your admin login, which is left untouched so you stay signed in. This cannot be undone.
        </p>

        <button type="button" onclick="document.getElementById('resetAllModal').showModal()"
            class="inline-flex items-center gap-2 bg-red-600 text-white px-5 py-2.5 rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
            Reset All Data
        </button>
    </div>
</div>

<dialog id="resetAllModal" class="rounded-lg shadow-lg p-6 w-full max-w-md">
    <form action="{{ route('settings.reset-all') }}" method="POST">
        @csrf
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Reset all data?</h3>
        <p class="text-sm text-gray-600 mb-4">
            This permanently deletes every sale, stock record, outlet, employee, payroll period, and everything else
            except your admin account. There is no undo.
        </p>
        <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="font-mono font-bold">RESET</span> to confirm</label>
        <input type="text" name="confirm" required autocomplete="off" class="form-input mb-4" placeholder="RESET">
        <div class="flex gap-2 justify-end">
            <button type="button" onclick="document.getElementById('resetAllModal').close()" class="px-4 py-2 text-gray-700 border rounded">Cancel</button>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Reset Everything</button>
        </div>
    </form>
</dialog>
@endsection