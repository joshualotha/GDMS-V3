<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\ReferenceGenerator;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('category');

        if ($request->category_id) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->date_from) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();

        return view('expenses.index', compact('expenses', 'categories'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $categoryWarning = $categories->isEmpty() ? 'Please create at least one Expense Category in Settings before adding expenses.' : null;
        return view('expenses.create', compact('categories', 'categoryWarning'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'reference' => 'nullable|string',
        ]);

        $validated['expense_number'] = ReferenceGenerator::generateExpenseNumber();

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense created.');
    }
}