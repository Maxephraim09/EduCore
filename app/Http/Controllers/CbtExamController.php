<?php

namespace App\Http\Controllers;

use App\Models\CbtExam;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CbtExamController extends Controller
{
    public function index()
    {
        $exams = CbtExam::with(['subject', 'class', 'createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('cbt.exams.index', compact('exams'));
    }

    public function create()
    {
        $subjects = Subject::where('is_active', true)->get();
        $classes = ClassModel::where('is_active', true)->get();
        $terms = ['First Term', 'Second Term', 'Third Term'];
        $types = ['test', 'exam', 'quiz', 'assignment'];
        
        return view('cbt.exams.create', compact('subjects', 'classes', 'terms', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'type' => 'required|string',
            'term' => 'required|string',
            'academic_year' => 'required|string',
            'duration_minutes' => 'required|integer|min:1|max:180',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
        ]);

        try {
            DB::beginTransaction();

            $exam = CbtExam::create([
                'title' => $request->title,
                'code' => 'CBT-' . strtoupper(Str::random(8)),
                'subject_id' => $request->subject_id,
                'class_id' => $request->class_id,
                'created_by' => auth()->id(),
                'description' => $request->description,
                'type' => $request->type,
                'term' => $request->term,
                'academic_year' => $request->academic_year,
                'duration_minutes' => $request->duration_minutes,
                'total_marks' => $request->total_marks,
                'passing_marks' => $request->passing_marks,
                'is_randomized' => $request->has('is_randomized'),
                'show_results_immediately' => $request->has('show_results_immediately'),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'draft',
            ]);

            DB::commit();

            return redirect()->route('cbt.exams.show', $exam->id)
                ->with('success', 'CBT Exam created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating exam: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $exam = CbtExam::with(['subject', 'class', 'questions', 'createdBy', 'approvedBy'])
            ->findOrFail($id);
        
        $stats = [
            'total_questions' => $exam->questions->count(),
            'total_attempts' => $exam->attempts()->count(),
            'average_score' => $exam->attempts()->avg('percentage') ?? 0,
            'pass_rate' => $exam->attempts()->where('score', '>=', $exam->passing_marks)->count() > 0 
                ? ($exam->attempts()->where('score', '>=', $exam->passing_marks)->count() / $exam->attempts()->count()) * 100 
                : 0,
        ];
        
        return view('cbt.exams.show', compact('exam', 'stats'));
    }

    public function edit($id)
    {
        $exam = CbtExam::findOrFail($id);
        $subjects = Subject::where('is_active', true)->get();
        $classes = ClassModel::where('is_active', true)->get();
        $terms = ['First Term', 'Second Term', 'Third Term'];
        $types = ['test', 'exam', 'quiz', 'assignment'];
        
        return view('cbt.exams.edit', compact('exam', 'subjects', 'classes', 'terms', 'types'));
    }

    public function update(Request $request, $id)
    {
        $exam = CbtExam::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'type' => 'required|string',
            'term' => 'required|string',
            'academic_year' => 'required|string',
            'duration_minutes' => 'required|integer|min:1|max:180',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        try {
            DB::beginTransaction();

            $exam->update([
                'title' => $request->title,
                'subject_id' => $request->subject_id,
                'class_id' => $request->class_id,
                'description' => $request->description,
                'type' => $request->type,
                'term' => $request->term,
                'academic_year' => $request->academic_year,
                'duration_minutes' => $request->duration_minutes,
                'total_marks' => $request->total_marks,
                'passing_marks' => $request->passing_marks,
                'is_randomized' => $request->has('is_randomized'),
                'show_results_immediately' => $request->has('show_results_immediately'),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            DB::commit();

            return redirect()->route('cbt.exams.show', $exam->id)
                ->with('success', 'CBT Exam updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating exam: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $exam = CbtExam::findOrFail($id);
            
            if ($exam->attempts()->exists()) {
                return back()->with('error', 'Cannot delete exam with attempts.');
            }
            
            $exam->delete();

            return redirect()->route('cbt.exams.index')
                ->with('success', 'Exam deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting exam: ' . $e->getMessage());
        }
    }

    public function submitForApproval($id)
    {
        $exam = CbtExam::findOrFail($id);
        
        if ($exam->questions()->count() < 1) {
            return back()->with('error', 'Exam must have at least 1 question.');
        }
        
        $exam->update([
            'status' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return redirect()->route('cbt.exams.show', $exam->id)
            ->with('success', 'Exam submitted for approval.');
    }

    public function approve($id)
    {
        $exam = CbtExam::findOrFail($id);
        
        $exam->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('cbt.exams.show', $exam->id)
            ->with('success', 'Exam approved successfully!');
    }

    public function publish($id)
    {
        $exam = CbtExam::findOrFail($id);
        
        if ($exam->status !== 'approved') {
            return back()->with('error', 'Exam must be approved before publishing.');
        }
        
        $exam->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return redirect()->route('cbt.exams.show', $exam->id)
            ->with('success', 'Exam published successfully!');
    }

    public function close($id)
    {
        $exam = CbtExam::findOrFail($id);
        
        $exam->update([
            'status' => 'closed',
        ]);

        return redirect()->route('cbt.exams.show', $exam->id)
            ->with('success', 'Exam closed successfully!');
    }

    public function autoConvertToTermExam($id)
    {
        $exam = CbtExam::with(['subject', 'class', 'attempts'])->findOrFail($id);
        
        try {
            DB::beginTransaction();
            
            // Create regular exam from CBT
            $regularExam = \App\Models\Exam::create([
                'name' => $exam->title . ' (CBT)',
                'code' => 'CBT-' . $exam->code,
                'subject_id' => $exam->subject_id,
                'class_id' => $exam->class_id,
                'term' => $exam->term,
                'academic_year' => $exam->academic_year,
                'start_date' => $exam->start_date,
                'end_date' => $exam->end_date,
                'max_marks' => $exam->total_marks,
                'passing_marks' => $exam->passing_marks,
                'is_published' => true,
                'description' => 'Auto-converted from CBT: ' . $exam->title,
            ]);
            
            // Convert attempts to results
            foreach ($exam->attempts as $attempt) {
                if ($attempt->status === 'completed') {
                    \App\Models\Result::create([
                        'student_id' => $attempt->student_id,
                        'exam_id' => $regularExam->id,
                        'subject_id' => $exam->subject_id,
                        'class_id' => $exam->class_id,
                        'total_score' => $attempt->score ?? 0,
                        'grade' => $attempt->grade,
                        'remark' => $attempt->remarks ?? 'CBT Exam',
                        'is_published' => true,
                    ]);
                }
            }
            
            $exam->update([
                'status' => 'archived',
                'is_active' => false,
            ]);
            
            DB::commit();

            return redirect()->route('exams.show', $regularExam->id)
                ->with('success', 'CBT exam converted to term exam successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error converting exam: ' . $e->getMessage());
        }
    }
}