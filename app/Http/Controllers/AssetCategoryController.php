<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::orderBy('name')->paginate(15);
        return view('assets.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:asset_categories',
            'depreciation_rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string'
        ]);

        AssetCategory::create($request->all());

        return redirect()->route('asset-categories.index')
            ->with('success', 'Asset category created successfully!');
    }

    public function update(Request $request, $id)
    {
        $category = AssetCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'depreciation_rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string'
        ]);

        $category->update($request->all());

        return redirect()->route('asset-categories.index')
            ->with('success', 'Asset category updated successfully!');
    }

    public function destroy($id)
    {
        $category = AssetCategory::findOrFail($id);
        
        // Check if category has assets
        if ($category->assets()->count() > 0) {
            return back()->with('error', 'Cannot delete category with associated assets.');
        }
        
        $category->delete();

        return redirect()->route('asset-categories.index')
            ->with('success', 'Asset category deleted successfully!');
    }
}