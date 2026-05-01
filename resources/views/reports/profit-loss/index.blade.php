@extends('layouts.app')

@section('title', 'Profit & Loss Report')

@section('header', 'Profit & Loss Report')

@section('content')
<form method="GET" class="bg-white p-4 rounded-lg shadow mb-4 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-xs text-gray-500">Date From</label>
        <input type="date" name="date_from" value="{{ request('date_from', date('Y-m-01')) }}" class="border rounded px-2 py-1">
    </div>
    <div>
        <label class="block text-xs text-gray-500">Date To</label>
        <input type="date" name="date_to" value="{{ request('date_to', date('Y-m-d')) }}" class="border rounded px-2 py-1">
    </div>
    <button type="submit" class="bg-gray-600 text-white px-4 py-1 rounded hover:bg-gray-700">Generate</button>
    <a href="{{ route('reports.profit-loss.export', request()->query()) }}" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Export PDF</a>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full">
        <tbody>
            <tr class="border-b bg-gray-100">
                <td colspan="2" class="px-4 py-2 font-bold">REVENUE</td>
            </tr>
            <tr class="border-b">
                <td class="px-4 py-2 pl-8">Total Sales Revenue</td>
                <td class="px-4 py-2 text-right">{{ number_format($totalRevenue, 2) }}</td>
            </tr>
            <tr class="border-b font-bold bg-gray-50">
                <td class="px-4 py-2">TOTAL REVENUE</td>
                <td class="px-4 py-2 text-right">{{ number_format($totalRevenue, 2) }}</td>
            </tr>
            
            <tr class="border-b bg-gray-100">
                <td colspan="2" class="px-4 py-2 font-bold">COST OF GOODS SOLD</td>
            </tr>
            <tr class="border-b font-bold bg-gray-50">
                <td class="px-4 py-2">TOTAL COGS</td>
                <td class="px-4 py-2 text-right text-red-600">-{{ number_format($totalCogs, 2) }}</td>
            </tr>
            
            <tr class="border-b bg-gray-100">
                <td colspan="2" class="px-4 py-2 font-bold">GROSS PROFIT</td>
            </tr>
            <tr class="border-b font-bold text-lg bg-gray-50">
                <td class="px-4 py-2">GROSS PROFIT</td>
                <td class="px-4 py-2 text-right {{ $grossProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($grossProfit, 2) }}
                </td>
            </tr>
            
            <tr class="border-b bg-gray-100">
                <td colspan="2" class="px-4 py-2 font-bold">OPERATING EXPENSES</td>
            </tr>
            <tr class="border-b">
                <td class="px-4 py-2 pl-8">Fuel Costs</td>
                <td class="px-4 py-2 text-right text-red-600">-{{ number_format($fuelCost, 2) }}</td>
            </tr>
            <tr class="border-b">
                <td class="px-4 py-2 pl-8">Payroll Costs</td>
                <td class="px-4 py-2 text-right text-red-600">-{{ number_format($payrollCost, 2) }}</td>
            </tr>
            <tr class="border-b">
                <td class="px-4 py-2 pl-8">Asset Depreciation</td>
                <td class="px-4 py-2 text-right text-red-600">-{{ number_format($depreciationCost, 2) }}</td>
            </tr>
            <tr class="border-b">
                <td class="px-4 py-2 pl-8">Other Expenses</td>
                <td class="px-4 py-2 text-right text-red-600">-{{ number_format($otherExpenses, 2) }}</td>
            </tr>
            <tr class="border-b font-bold bg-gray-50">
                <td class="px-4 py-2">TOTAL OPERATING EXPENSES</td>
                <td class="px-4 py-2 text-right text-red-600">-{{ number_format($totalOperatingExpenses, 2) }}</td>
            </tr>
            
            <tr class="border-b bg-gray-200 font-bold text-xl">
                <td class="px-4 py-3">NET PROFIT / (LOSS)</td>
                <td class="px-4 py-3 text-right {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($netProfit, 2) }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection