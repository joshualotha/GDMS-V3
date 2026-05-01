@extends('layouts.app')

@section('title', 'General Settings')

@section('header', 'General Settings')

@section('content')
<form action="{{ route('settings.update') }}" method="POST" class="max-w-xl bg-white rounded-lg shadow p-6 space-y-6">
    @csrf

    <div>
        <label for="business_name" class="block text-sm font-medium text-gray-700">Business Name</label>
        <input type="text" name="business_name" id="business_name" value="{{ old('business_name', $settings['business_name']) }}" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('business_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
        <textarea name="address" id="address" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $settings['address']) }}</textarea>
        @error('address')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
            <input type="text" name="currency" id="currency" value="{{ old('currency', $settings['currency']) }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('currency')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="financial_year_start" class="block text-sm font-medium text-gray-700">Financial Year Start</label>
            <select name="financial_year_start" id="financial_year_start" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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

    <div class="flex justify-end">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Save Settings
        </button>
    </div>
</form>
@endsection