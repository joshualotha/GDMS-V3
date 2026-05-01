<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('expenses.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('expenses.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string',
        ]);

        ExpenseCategory::create($validated);

        return redirect()->route('expense-categories.index')->with('success', 'Category created.');
    }

    public function edit(ExpenseCategory $expense_category)
    {
        return view('expenses.categories.edit', compact('expense_category'));
    }

    public function update(Request $request, ExpenseCategory $expense_category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expense_category->id,
            'description' => 'nullable|string',
        ]);

        $expense_category->update($validated);

        return redirect()->route('expense-categories.index')->with('success', 'Category updated.');
    }

    public function toggle(ExpenseCategory $expense_category)
    {
        $expense_category->update(['is_active' => !$expense_category->is_active]);

        $status = $expense_category->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Category {$status}.");
    }
}