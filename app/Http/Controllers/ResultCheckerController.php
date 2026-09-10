<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\ResultCheckerPin;
use App\Models\Result;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultCheckerController extends Controller
{
    public function index()
    {
        return view('result-checker.index', [
            'exams' => Exam::where('is_published', true)->latest()->get(),
            'classes' => ClassModel::where('is_active', true)->orderBy('name')->get(),
            'pins' => ResultCheckerPin::with(['student', 'exam'])->latest()->paginate(50),
        ]);
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        $students = Student::where('class_id', $data['class_id'])->where('is_active', true)->get();
        $created = collect();

        DB::transaction(function () use ($students, $data, &$created) {
            foreach ($students as $student) {
                $pin = ResultCheckerPin::firstOrNew(['student_id' => $student->id, 'exam_id' => $data['exam_id']]);
                if (!$pin->exists) {
                    do {
                        $value = (string) random_int(100000, 999999);
                    } while (ResultCheckerPin::where('exam_id', $data['exam_id'])->where('pin', $value)->exists());
                    $pin->pin = $value;
                    $pin->generated_by = auth()->id();
                    $pin->save();
                }
                $created->push($pin->load(['student', 'exam']));
            }
        });

        return view('result-checker.print', ['pins' => $created, 'exam' => Exam::findOrFail($data['exam_id'])]);
    }

    public function verifyForm()
    {
        return view('result-checker.verify');
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'registration_number' => 'required|string|max:100',
            'pin' => 'required|digits:6',
        ]);

        $pin = ResultCheckerPin::with(['student', 'exam'])
            ->where('pin', $data['pin'])
            ->whereHas('student', fn ($query) => $query->where('admission_number', $data['registration_number']))
            ->first();

        if (!$pin || !isReportCardPublished($pin->exam_id, $pin->student->class_id)) {
            return back()->withErrors(['registration_number' => 'The registration number or PIN is invalid, or the result is not published.'])->withInput();
        }

        if (!Result::where('exam_id', $pin->exam_id)->where('student_id', $pin->student_id)->exists()) {
            return back()->withErrors(['registration_number' => 'No results were found for this student and term.'])->withInput();
        }

        return view('result-checker.result', compact('pin'));
    }

    public function verifyQr(string $registrationNumber, string $pin)
    {
        abort_unless(preg_match('/^\d{6}$/', $pin), 404);

        $record = ResultCheckerPin::with(['student', 'exam'])
            ->where('pin', $pin)
            ->whereHas('student', fn ($query) => $query->where('admission_number', $registrationNumber))
            ->first();

        if (!$record || !isReportCardPublished($record->exam_id, $record->student->class_id)) {
            return redirect()->route('result-checker.form')->withErrors(['registration_number' => 'This result QR code is invalid or the result is not published.']);
        }

        return view('result-checker.result', ['pin' => $record]);
    }

    public function download(int $pinId)
    {
        $pin = ResultCheckerPin::with(['student', 'exam'])->find($pinId);

        if (!$pin || !$pin->student || !$pin->exam || !isReportCardPublished($pin->exam_id, $pin->student->class_id)) {
            return redirect()->route('result-checker.form')->withErrors([
                'registration_number' => 'This result is unavailable or has not been published.',
            ]);
        }
        $report = app(ReportCardController::class)->buildStudentReportCardData($pin->exam, $pin->student, false);
        if ($report['results']->isEmpty()) {
            return redirect()->route('result-checker.form')->withErrors([
                'registration_number' => 'No published results were found for this PIN.',
            ]);
        }

        $pdf = Pdf::loadView('report-cards.pdf', array_merge($report, [
            'exam' => $pin->exam,
            'student' => $pin->student,
            'results' => $report['results'],
            'summary' => $report['summary'],
            'position' => $report['position'],
            'totalStudents' => $report['totalStudents'],
            'subjectData' => $report['subjectData'],
        ]));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('report-card-' . $pin->student->admission_number . '-' . $pin->exam->code . '.pdf');
    }
}
