<?php

namespace App\Http\Controllers;

use App\Models\GradeScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GradeScaleController extends Controller
{
    /**
     * Display a listing of grade scales.
     */
    public function index()
    {
        if (!Schema::hasTable('grade_scales')) {
            return redirect()->back()->with('error', 'Grade scales table does not exist.');
        }

        $gradeScales = GradeScale::where('is_active', true)
            ->orderBy('min_score', 'desc')
            ->get();

        return view('grading.scale.index', compact('gradeScales'));
    }

    /**
     * Show the form for creating a new grade scale.
     */
    public function create()
    {
        return view('grading.scale.create');
    }

    /**
     * Store a newly created grade scale.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade' => 'required|string|max:2|unique:grade_scales,grade',
            'min_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100|gte:min_score',
            'remark' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Check if 'name' column exists and add it
        $columns = Schema::getColumnListing('grade_scales');
        if (in_array('name', $columns)) {
            $validated['name'] = $validated['grade'];
        }

        DB::beginTransaction();
        try {
            GradeScale::create($validated);
            DB::commit();

            return redirect()
                ->route('grading.scale')
                ->with('success', 'Grade scale created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating grade scale: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create grade scale. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified grade scale.
     */
    public function edit(GradeScale $gradeScale)
    {
        return view('grading.scale.edit', compact('gradeScale'));
    }

    /**
     * Update the specified grade scale.
     */
    public function update(Request $request, GradeScale $gradeScale)
    {
        $validated = $request->validate([
            'grade' => 'required|string|max:2|unique:grade_scales,grade,' . $gradeScale->id,
            'min_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100|gte:min_score',
            'remark' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        DB::beginTransaction();
        try {
            $gradeScale->update($validated);
            DB::commit();

            return redirect()
                ->route('grading.scale')
                ->with('success', 'Grade scale updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating grade scale: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update grade scale. Please try again.');
        }
    }

    /**
     * Remove the specified grade scale.
     */
    public function destroy(GradeScale $gradeScale)
    {
        DB::beginTransaction();
        try {
            $gradeScale->delete();
            DB::commit();

            return redirect()
                ->route('grading.scale')
                ->with('success', 'Grade scale deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting grade scale: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Failed to delete grade scale. Please try again.');
        }
    }

    /**
     * Get active grade scales for AJAX consumers.
     */
    public function getActive()
    {
        $gradeScales = GradeScale::where('is_active', true)
            ->orderBy('min_score', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $gradeScales,
        ]);
    }

    /**
     * Toggle a grade scale's active status.
     */
    public function toggleStatus(Request $request, GradeScale $gradeScale)
    {
        $gradeScale->is_active = ! $gradeScale->is_active;
        $gradeScale->save();

        return response()->json([
            'success' => true,
            'message' => 'Grade scale status updated successfully.',
            'data' => $gradeScale,
        ]);
    }
}
