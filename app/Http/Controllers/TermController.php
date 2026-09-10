<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TermController extends Controller
{
    public function index(AcademicYear $academicYear)
    {
        $terms = $academicYear->terms()->orderBy('sequence')->get();
        return view('terms.index', compact('academicYear', 'terms'));
    }

    public function create(AcademicYear $academicYear)
    {
        return view('terms.create', compact('academicYear'));
    }

    public function store(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'sequence' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $validated['academic_year_id'] = $academicYear->id;
            $validated['slug'] = Term::generateSlug($validated['name'], $academicYear->name);

            // If this is the first term, make it current.
            if ($academicYear->terms()->count() === 0) {
                $validated['is_current'] = true;
            } else {
                $validated['is_current'] = false;
            }

            Term::create($validated);

            DB::commit();

            return redirect()->route('academic-years.show', $academicYear)
                ->with('success', 'Term created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create term: ' . $e->getMessage());
        }
    }

    public function edit(Term $term)
    {
        return view('terms.edit', compact('term'));
    }

    public function update(Request $request, Term $term)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'sequence' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $academicYear = $term->academicYear;

            $validated['slug'] = Term::generateSlug($validated['name'], $academicYear->name);

            $term->update($validated);

            DB::commit();

            return redirect()->route('academic-years.show', $term->academicYear)
                ->with('success', 'Term updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update term: ' . $e->getMessage());
        }
    }

    public function setCurrent(Term $term)
    {
        DB::beginTransaction();
        try {
            $academicYear = $term->academicYear;

            $academicYear->terms()->update(['is_current' => false]);
            $term->update(['is_current' => true]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Term set as current.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to set current term.');
        }
    }

    public function destroy(Term $term)
    {
        // If you later link Term -> Result, enforce deletion rules here.
        DB::beginTransaction();
        try {
            $academicYear = $term->academicYear;

            $term->delete();

            DB::commit();

            return redirect()->route('academic-years.show', $academicYear)
                ->with('success', 'Term deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to delete term.');
        }
    }
}

