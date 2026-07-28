@extends('layouts.app')

@section('title', 'Add Expense')

@section('header', 'Add Expense')

@section('content')
@if($categoryWarning)
    <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
        {{ $categoryWarning }}
        <a href="{{ route('expense-categories.index') }}" class="underline font-medium">Create Category</a>
    </div>
@endif

<form action="{{ route('expenses.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow max-w-lg">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Category *</label>
            <div class="mt-1 flex gap-2">
                <select name="expense_category_id" id="expense_category_id" required class="form-select">
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="document.getElementById('newCategoryModal').showModal()"
                    class="shrink-0 w-10 h-10 flex items-center justify-center border rounded hover:bg-gray-50 text-lg font-medium" title="Add a new category">+</button>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Date *</label>
            <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="mt-1 form-input">
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Description *</label>
            <textarea name="description" required rows="2" class="mt-1 form-input"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Amount *</label>
            <input type="number" name="amount" step="0.01" required class="mt-1 form-input">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Reference</label>
            <input type="text" name="reference" class="mt-1 form-input" placeholder="Invoice #, Receipt #, etc.">
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Related Asset</label>
            <select name="asset_id" class="mt-1 form-select">
                <option value="">-- None --</option>
                @foreach($assets as $asset)
                    <option value="{{ $asset->id }}" {{ (string) $selectedAssetId === (string) $asset->id ? 'selected' : '' }}>{{ $asset->name }}{{ $asset->plate_number ? ' ('.$asset->plate_number.')' : '' }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Tag a vehicle repair, service, or other asset-specific cost so it shows up on that asset's page.</p>
        </div>
    </div>

    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('expenses.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Cancel</a>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save</button>
    </div>
</form>

<dialog id="newCategoryModal" class="rounded-lg shadow-lg p-6 w-full max-w-sm">
    <h3 class="text-lg font-semibold mb-4">New Expense Category</h3>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
        <input type="text" id="newCategoryName" class="form-input">
    </div>
    <p id="newCategoryError" class="text-sm text-red-600 mb-3 hidden"></p>
    <div class="flex gap-2 justify-end">
        <button type="button" onclick="document.getElementById('newCategoryModal').close()" class="px-4 py-2 text-gray-700 border rounded">Cancel</button>
        <button type="button" onclick="saveNewCategory()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save</button>
    </div>
</dialog>

<script>
function saveNewCategory() {
    var name = document.getElementById('newCategoryName').value.trim();
    var errorEl = document.getElementById('newCategoryError');
    errorEl.classList.add('hidden');

    if (!name) {
        errorEl.textContent = 'Name is required.';
        errorEl.classList.remove('hidden');
        return;
    }

    fetch('{{ route('expense-categories.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ name: name })
    })
    .then(function(res) {
        if (!res.ok) {
            return res.json().then(function(data) { throw data; });
        }
        return res.json();
    })
    .then(function(category) {
        var select = document.getElementById('expense_category_id');
        var option = document.createElement('option');
        option.value = category.id;
        option.textContent = category.name;
        option.selected = true;
        select.appendChild(option);

        document.getElementById('newCategoryName').value = '';
        document.getElementById('newCategoryModal').close();
    })
    .catch(function(data) {
        var message = data && data.errors && data.errors.name ? data.errors.name[0] : 'Could not create category.';
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    });
}
</script>
@endsection