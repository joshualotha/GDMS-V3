@extends('layouts.app')

@section('title', 'Fuel Stock')

@section('header', 'Fuel Stock')

@section('content')
<div class="grid grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Diesel</p>
        <p class="text-3xl font-bold">{{ $fuelStock['diesel']->litres ?? 0 }} L</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Petrol</p>
        <p class="text-3xl font-bold">{{ $fuelStock['petrol']->litres ?? 0 }} L</p>
    </div>
</div>

<div class="flex gap-4">
    <a href="{{ url('fuel/purchases') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">Purchases</a>
    <a href="{{ url('fuel/issues') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">Issues</a>
</div>
@endsection