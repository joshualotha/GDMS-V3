<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\ReferenceGenerator;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\CompanyAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('category', 'asset');

        if ($request->category_id) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->asset_id) {
            $query->where('asset_id', $request->asset_id);
        }

        if ($request->date_from) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $assets = CompanyAsset::where('status', 'active')->orderBy('name')->get();

        return view('expenses.index', compact('expenses', 'categories', 'assets'));
    }

    public function create(Request $request)
    {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $categoryWarning = $categories->isEmpty() ? 'Please create at least one Expense Category in Settings before adding expenses.' : null;
        $assets = CompanyAsset::where('status', 'active')->orderBy('name')->get();
        $selectedAssetId = $request->query('asset_id');

        return view('expenses.create', compact('categories', 'categoryWarning', 'assets', 'selectedAssetId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'asset_id' => 'nullable|exists:assets,id',
            'expense_date' => 'required|date',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'reference' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $validated['expense_number'] = ReferenceGenerator::generateExpenseNumber();
            Expense::create($validated);
        });

        return redirect()->route('expenses.index')
            ->with('success', 'Expense created.');
    }

    public function edit(Expense $expense)
    {
        if ($expense->fuel_purchase_id) {
            return redirect()->route('expenses.index')
                ->with('error', 'This expense was auto-generated from a fuel purchase. Edit or delete the fuel purchase instead.');
        }

        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $assets = CompanyAsset::where('status', 'active')->orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'categories', 'assets'));
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->fuel_purchase_id) {
            return redirect()->route('expenses.index')
                ->with('error', 'This expense was auto-generated from a fuel purchase. Edit or delete the fuel purchase instead.');
        }

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'asset_id' => 'nullable|exists:assets,id',
            'expense_date' => 'required|date',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'reference' => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->fuel_purchase_id) {
            return redirect()->route('expenses.index')
                ->with('error', 'This expense was auto-generated from a fuel purchase. Delete the fuel purchase instead — that will remove this expense too.');
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted.');
    }
}