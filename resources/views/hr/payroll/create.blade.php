@extends('layouts.app')

@section('title', 'Generate Payroll')

@section('header', 'Generate Payroll')

@section('content')
<form action="{{ route('payroll.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow max-w-md">
    @csrf
    
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Period Month *</label>
        <select name="period_month" required class="mt-1 w-full border rounded px-3 py-2">
            <option value="">Select Month</option>
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
            @endfor
        </select>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Period Year *</label>
        <select name="period_year" required class="mt-1 w-full border rounded px-3 py-2">
            <option value="">Select Year</option>
            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>

    <p class="text-sm text-gray-600 mb-4">
        This will create payroll items for all active employees with their basic salary pre-filled. You can edit allowances and deductions before approval.
    </p>

    <div class="flex justify-end gap-3">
        <a href="{{ route('payroll.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Generate</button>
    </div>
</form>
@endsection