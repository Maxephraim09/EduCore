<?php

namespace App\Http\Controllers;

use App\Models\CbtExam;
use App\Models\CbtQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CbtQuestionController extends Controller
{
    public function create($examId)
    {
        $exam = CbtExam::with('questions')->findOrFail($examId);
        return view('cbt.questions.create', compact('exam'));
    }

    public function store(Request $request, $examId)
    {
        $exam = CbtExam::findOrFail($examId);
        
        $request->validate([
            'type' => 'required|in:multiple_choice,true_false,fill_blank,essay',
            'question' => 'required|string',
            'marks' => 'required|integer|min:1',
            'options' => 'nullable|array',
            'correct_answer' => 'nullable|string',
            'explanation' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $question = CbtQuestion::create([
                'cbt_exam_id' => $exam->id,
                'type' => $request->type,
                'question' => $request->question,
                'marks' => $request->marks,
                'options' => $request->type === 'multiple_choice' ? $request->options : null,
                'correct_answer' => $request->correct_answer,
                'explanation' => $request->explanation,
                'order' => $exam->questions()->count() + 1,
                'is_active' => true,
            ]);

            // Update total questions count
            $exam->update([
                'total_questions' => $exam->questions()->count()
            ]);

            DB::commit();

            if ($request->has('add_another')) {
                return redirect()->route('cbt.questions.create', $exam->id)
                    ->with('success', 'Question added! Add another.');
            }

            return redirect()->route('cbt.exams.show', $exam->id)
                ->with('success', 'Question added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error adding question: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $question = CbtQuestion::with('exam')->findOrFail($id);
        return view('cbt.questions.edit', compact('question'));
    }

    public function update(Request $request, $id)
    {
        $question = CbtQuestion::findOrFail($id);
        
        $request->validate([
            'type' => 'required|in:multiple_choice,true_false,fill_blank,essay',
            'question' => 'required|string',
            'marks' => 'required|integer|min:1',
            'options' => 'nullable|array',
            'correct_answer' => 'nullable|string',
            'explanation' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $question->update([
                'type' => $request->type,
                'question' => $request->question,
                'marks' => $request->marks,
                'options' => $request->type === 'multiple_choice' ? $request->options : null,
                'correct_answer' => $request->correct_answer,
                'explanation' => $request->explanation,
            ]);

            DB::commit();

            return redirect()->route('cbt.exams.show', $question->cbt_exam_id)
                ->with('success', 'Question updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating question: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $question = CbtQuestion::findOrFail($id);
            $examId = $question->cbt_exam_id;
            
            $question->delete();

            // Update total questions count
            $exam = CbtExam::find($examId);
            if ($exam) {
                $exam->update([
                    'total_questions' => $exam->questions()->count()
                ]);
            }

            return redirect()->route('cbt.exams.show', $examId)
                ->with('success', 'Question deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting question: ' . $e->getMessage());
        }
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'exists:cbt_questions,id',
        ]);

        try {
            foreach ($request->questions as $index => $questionId) {
                CbtQuestion::where('id', $questionId)->update(['order' => $index + 1]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}