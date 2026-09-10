<?php

namespace App\Http\Controllers;

use App\Models\ClassCategory;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassCategoryController extends Controller
{
    public function index()
    {
        $categories = ClassCategory::withCount('classes')
            ->orderBy('name')
            ->paginate(15);

        return view('class-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('class-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:class_categories,name',
            'code' => 'required|string|max:50|unique:class_categories,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        ClassCategory::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('class-categories.index')
            ->with('success', 'Class category created successfully.');
    }

    public function edit($id)
    {
        $category = ClassCategory::findOrFail($id);
        return view('class-categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = ClassCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:class_categories,name,' . $id,
            'code' => 'required|string|max:50|unique:class_categories,code,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('class-categories.index')
            ->with('success', 'Class category updated successfully.');
    }

    public function assignClass(Request $request, $id)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $category = ClassCategory::findOrFail($id);
        $class = ClassModel::findOrFail($request->class_id);

        $class->update(['class_category_id' => $category->id]);

        return redirect()->route('class-categories.index')
            ->with('success', $class->full_class_name . ' assigned to category ' . $category->name);
    }

    public function assignForm($id)
    {
        $category = ClassCategory::findOrFail($id);
        $classes = ClassModel::orderBy('name')->orderBy('section')->get();
        return view('class-categories.assign', compact('category', 'classes'));
    }
}
