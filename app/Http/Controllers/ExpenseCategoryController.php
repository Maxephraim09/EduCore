<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::orderBy('name')->paginate(15);
        return view('expenses.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:expense_categories',
            'description' => 'nullable|string'
        ]);

        ExpenseCategory::create($request->all());

        return redirect()->route('expense-categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function update(Request $request, $id)
    {
        $category = ExpenseCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $category->update($request->all());

        return redirect()->route('expense-categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        
        // Check if category has expenses
        if ($category->expenses()->count() > 0) {
            return back()->with('error', 'Cannot delete category with associated expenses.');
        }
        
        $category->delete();

        return redirect()->route('expense-categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}