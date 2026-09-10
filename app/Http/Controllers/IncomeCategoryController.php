<?php

namespace App\Http\Controllers;

use App\Models\IncomeCategory;
use Illuminate\Http\Request;

class IncomeCategoryController extends Controller
{
    public function index()
    {
        $categories = IncomeCategory::orderBy('name')->paginate(15);
        return view('incomes.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:income_categories',
            'description' => 'nullable|string'
        ]);

        IncomeCategory::create($request->all());

        return redirect()->route('income-categories.index')
            ->with('success', 'Income category created successfully!');
    }

    public function update(Request $request, $id)
    {
        $category = IncomeCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $category->update($request->all());

        return redirect()->route('income-categories.index')
            ->with('success', 'Income category updated successfully!');
    }

    public function destroy($id)
    {
        $category = IncomeCategory::findOrFail($id);
        
        // Check if category has incomes
        if ($category->incomes()->count() > 0) {
            return back()->with('error', 'Cannot delete category with associated income records.');
        }
        
        $category->delete();

        return redirect()->route('income-categories.index')
            ->with('success', 'Income category deleted successfully!');
    }
}