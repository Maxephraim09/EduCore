<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::withCount('terms')
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        $currentYear = AcademicYear::getCurrent();

        return view('academic-years.index', compact('academicYears', 'currentYear'));
    }

    public function create()
    {
        return view('academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // If no academic year exists, make this the current one.
            if (AcademicYear::count() === 0) {
                $validated['is_current'] = true;
            } else {
                $validated['is_current'] = false;
            }

            AcademicYear::create($validated);

            DB::commit();

            return redirect()->route('academic-years.index')
                ->with('success', 'Academic year created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create academic year: ' . $e->getMessage());
        }
    }

    public function show(AcademicYear $academicYear)
    {
        $terms = $academicYear->terms()->orderBy('sequence')->get();

        $stats = [
            'total_terms' => $terms->count(),
            'active_terms' => $terms->where('status', 'active')->count(),
            'duration' => $academicYear->duration,
            // Student count by admission_date is repo-specific and may not align.
            'students_count' => null,
        ];

        return view('academic-years.show', compact('academicYear', 'terms', 'stats'));
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_years,name,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $academicYear->update($validated);
            DB::commit();

            return redirect()->route('academic-years.index')
                ->with('success', 'Academic year updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update academic year: ' . $e->getMessage());
        }
    }

    public function setCurrent(AcademicYear $academicYear)
    {
        DB::beginTransaction();
        try {
            AcademicYear::query()->update(['is_current' => false]);
            $academicYear->update(['is_current' => true]);

            DB::commit();

            return redirect()->route('academic-years.index')
                ->with('success', 'Academic year set as current.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to set current academic year.');
        }
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->terms()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete academic year with associated terms.');
        }

        if ($academicYear->is_current) {
            return redirect()->back()
                ->with('error', 'Cannot delete the current academic year.');
        }

        DB::beginTransaction();
        try {
            $academicYear->delete();
            DB::commit();

            return redirect()->route('academic-years.index')
                ->with('success', 'Academic year deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to delete academic year.');
        }
    }
}

