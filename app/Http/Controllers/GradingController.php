<?php

namespace App\Http\Controllers;

use App\Models\Grading;
use App\Models\GradeScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class GradingController extends Controller
{
    /**
     * Display grading settings.
     */
    public function index()
    {
        // Check if table exists
        if (!Schema::hasTable('gradings')) {
            return redirect()->back()->with('error', 'Grading table does not exist. Please run migrations.');
        }

        // Get the first (and only) grading record, or create default if none exists
        $grading = Grading::first();
        
        if (!$grading) {
            // Create default grading if none exists
            $grading = Grading::create([
                'ca1_max' => 10,
                'ca2_max' => 10,
                'ca3_max' => 10,
                'exam_max' => 60,
                'practical_max' => 10,
                'passing_marks' => 40,
                'has_practical' => true,
                'total_max' => 100,
                'created_by' => Auth::id(),
            ]);
        }

        // Get grade scales
        $gradeScales = collect();
        if (Schema::hasTable('grade_scales')) {
            $gradeScales = GradeScale::where('is_active', true)
                ->orderBy('min_score', 'desc')
                ->get();
        }

        return view('grading.index', compact('grading', 'gradeScales'));
    }

    /**
     * Show the form for editing grading settings.
     */
    public function edit()
    {
        if (!Schema::hasTable('gradings')) {
            return redirect()->route('grading.index')->with('error', 'Grading table does not exist.');
        }

        $grading = Grading::first();
        
        if (!$grading) {
            $grading = new Grading([
                'ca1_max' => 10,
                'ca2_max' => 10,
                'ca3_max' => 10,
                'exam_max' => 60,
                'practical_max' => 10,
                'passing_marks' => 40,
                'has_practical' => true,
                'total_max' => 100,
            ]);
        }

        $gradeScales = collect();
        if (Schema::hasTable('grade_scales')) {
            $gradeScales = GradeScale::where('is_active', true)
                ->orderBy('min_score', 'desc')
                ->get();
        }

        return view('grading.index', compact('grading', 'gradeScales'));
    }

    /**
     * Update the grading settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'ca1_max' => 'required|numeric|min:0|max:30',
            'ca2_max' => 'required|numeric|min:0|max:30',
            'ca3_max' => 'required|numeric|min:0|max:30',
            'exam_max' => 'required|numeric|min:0|max:100',
            'practical_max' => 'nullable|numeric|min:0|max:30',
            'passing_marks' => 'required|numeric|min:0|max:100',
            'has_practical' => 'nullable|boolean',
            'total_max' => 'required|numeric|min:100|max:100',
        ]);

        $validated['has_practical'] = $request->has('has_practical') ? 1 : 0;
        
        if (!isset($validated['practical_max']) || $validated['has_practical'] == 0) {
            $validated['practical_max'] = 0;
        }

        DB::beginTransaction();
        try {
            $grading = Grading::first();
            
            if ($grading) {
                $grading->update($validated);
            } else {
                $validated['created_by'] = Auth::id();
                $grading = Grading::create($validated);
            }
            
            DB::commit();

            return redirect()
                ->route('grading.index')
                ->with('success', 'Grading settings updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating grading: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update grading settings. Please try again.');
        }
    }

    /**
     * Get grade for a given score.
     */
    public function getGrade($score)
    {
        if (!Schema::hasTable('grade_scales')) {
            return response()->json([
                'success' => false,
                'message' => 'Grade scales table does not exist.'
            ], 404);
        }

        $gradeScale = GradeScale::where('is_active', true)
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();

        if (!$gradeScale) {
            return response()->json([
                'success' => false,
                'message' => 'No grade found for the given score.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'grade' => $gradeScale->grade,
                'remark' => $gradeScale->remark,
                'color' => $gradeScale->color,
                'score' => $score
            ]
        ]);
    }

    /**
     * Get grading statistics.
     */
    public function getStats()
    {
        if (!Schema::hasTable('gradings')) {
            return response()->json([
                'success' => false,
                'message' => 'Grading table does not exist.'
            ], 404);
        }

        $grading = Grading::first();
        
        if (!$grading) {
            return response()->json([
                'success' => false,
                'message' => 'No grading settings found.'
            ], 404);
        }

        $stats = [
            'total_ca' => $grading->ca1_max + $grading->ca2_max + $grading->ca3_max,
            'exam' => $grading->exam_max,
            'practical' => $grading->has_practical ? $grading->practical_max : 0,
            'total' => $grading->total_max,
            'passing_marks' => $grading->passing_marks,
            'has_practical' => $grading->has_practical,
            'components' => [
                'ca1' => $grading->ca1_max,
                'ca2' => $grading->ca2_max,
                'ca3' => $grading->ca3_max,
                'exam' => $grading->exam_max,
                'practical' => $grading->practical_max,
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
