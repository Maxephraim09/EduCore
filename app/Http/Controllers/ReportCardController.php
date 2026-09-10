<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Result;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\GradeScale;
use App\Models\Subject;
use App\Models\ReportCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class ReportCardController extends Controller
{
    public function index()
    {
        $exams = Exam::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $classes = ClassModel::where('is_active', true)
            ->when(auth()->check() && auth()->user()->role === 'teacher', function ($query) {
                $query->where('class_teacher_id', getCurrentEmployeeId() ?? 0);
            })
            ->orderBy('name')
            ->get();
        
        return view('report-cards.index', compact('exams', 'classes'));
    }

    public function settings(Request $request)
    {
        $exams = Exam::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $classes = ClassModel::where('is_active', true)
            ->when(auth()->check() && auth()->user()->role === 'teacher', function ($query) {
                $query->where('class_teacher_id', getCurrentEmployeeId() ?? 0);
            })
            ->orderBy('name')
            ->get();

        $selectedExam = null;
        $selectedClass = null;
        $students = collect();
        $isPublished = false;

        if ($request->filled('exam_id') && $request->filled('class_id')) {
            $selectedExam = Exam::find($request->exam_id);
            $selectedClass = ClassModel::where('is_active', true)
                ->when(auth()->check() && auth()->user()->role === 'teacher', function ($query) {
                    $query->where('class_teacher_id', getCurrentEmployeeId() ?? 0);
                })
                ->find($request->class_id);
            if ($selectedExam && $selectedClass) {
                $students = Student::where('class_id', $selectedClass->id)
                    ->where('is_active', true)
                    ->orderBy('first_name')
                    ->get();

                $isPublished = isReportCardPublished($selectedExam->id, $selectedClass->id);
            }
        }

        return view('report-cards.reportcard-setting', compact(
            'exams', 'classes', 'selectedExam', 'selectedClass', 'students', 'isPublished'
        ));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'required|exists:classes,id',
            'report_type' => 'nullable|string|in:single,bulk',
            'student_id' => 'required_if:report_type,single|nullable|exists:students,id',
        ]);

        $exam = Exam::with(['examSubjects.subject', 'examSubjects.class'])
            ->findOrFail($request->exam_id);
        
        $class = ClassModel::with(['students' => function($q) {
            $q->where('is_active', true)->orderBy('first_name');
        }])->when(auth()->check() && auth()->user()->role === 'teacher', function ($query) {
            $query->where('class_teacher_id', getCurrentEmployeeId() ?? 0);
        })->findOrFail($request->class_id);

        // Get all results for this exam and class
        $results = Result::with(['student', 'subject'])
            ->where('exam_id', $request->exam_id)
            ->where('class_id', $request->class_id)
            ->get()
            ->groupBy('student_id');

        // If specific student requested
        if ($request->student_id) {
            $students = Student::where('id', $request->student_id)
                ->where('class_id', $request->class_id)
                ->get();
        } else {
            $students = $class->students;
        }

        // Calculate positions and summaries
        $reportData = $this->calculateReportData($students, $results, $exam);

        return view('report-cards.generate', compact('exam', 'class', 'reportData', 'students'));
    }

    public function generateRedirect()
    {
        return redirect()->route('report-cards.index');
    }

    public function bulkIndex()
    {
        $exams = Exam::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $classes = ClassModel::where('is_active', true)
            ->when(auth()->check() && auth()->user()->role === 'teacher', function ($query) {
                $query->where('class_teacher_id', getCurrentEmployeeId() ?? 0);
            })
            ->orderBy('name')
            ->get();

        return view('report-cards.bulk', compact('exams', 'classes'));
    }

    public function viewSingle($examId, $studentId)
    {
        $exam = Exam::findOrFail($examId);
        $student = Student::with('class')->findOrFail($studentId);

        $reportCard = $this->buildStudentReportCardData($exam, $student, false);
        $results = $reportCard['results'];

        if ($results->isEmpty()) {
            return redirect()->back()->with('error', 'No results found for this student.');
        }

        $summary = $reportCard['summary'];
        $position = $reportCard['position'];
        $totalStudents = $reportCard['totalStudents'];
        $subjectData = $reportCard['subjectData'];

        return view('report-cards.single', array_merge($reportCard, compact(
            'exam', 'student', 'results', 'summary', 'position', 'totalStudents', 'subjectData'
        )));
    }

    public function downloadPdf($examId, $studentId)
    {
        $exam = Exam::findOrFail($examId);
        $student = Student::with('class')->findOrFail($studentId);
        
        if ($student->class === null) {
            return redirect()->back()->with('error', 'Student is not assigned to a class.');
        }

        if (auth()->check() && auth()->user()->role === 'teacher' && $student->class->class_teacher_id !== getCurrentEmployeeId()) {
            return redirect()->back()->with('error', 'You can only download report cards for your own students.');
        }

        if (!isReportCardPublished($examId, $student->class_id)) {
            return redirect()->back()->with('error', 'Report cards for this exam and class are not published yet.');
        }

        $reportCard = $this->buildStudentReportCardData($exam, $student, false);
        $results = $reportCard['results'];

        if ($results->isEmpty()) {
            return redirect()->back()->with('error', 'No results found for this student.');
        }

        $summary = $reportCard['summary'];
        $position = $reportCard['position'];
        $totalStudents = $reportCard['totalStudents'];
        $subjectData = $reportCard['subjectData'];

        $data = array_merge($reportCard, compact('exam', 'student', 'results', 'summary', 'position', 'totalStudents', 'subjectData'));
        
        $pdf = Pdf::loadView('report-cards.pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('report-card-' . $student->admission_number . '-' . $exam->code . '.pdf');
    }

    public function bulkGenerate($examId, $classId)
    {
        $exam = Exam::findOrFail($examId);
        $class = ClassModel::findOrFail($classId);

        if (auth()->check() && auth()->user()->role === 'teacher' && $class->class_teacher_id !== getCurrentEmployeeId()) {
            return redirect()->back()->with('error', 'You can only generate bulk report cards for your own class.');
        }

        if (!isReportCardPublished($examId, $classId)) {
            return redirect()->back()->with('error', 'Report cards for this exam and class are not published yet.');
        }

        $students = Student::with('class')
            ->where('class_id', $classId)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'There are no active students in the selected class.');
        }

        $reportCards = $students
            ->map(fn ($student) => $this->buildStudentReportCardData($exam, $student, false))
            ->filter(fn ($reportCard) => $reportCard['results']->isNotEmpty())
            ->values();

        if ($reportCards->isEmpty()) {
            return redirect()->back()->with('error', 'No published results found for the selected class and exam.');
        }

        $pdf = Pdf::loadView('report-cards.bulk-print-pdf', compact('exam', 'class', 'reportCards'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('class-report-' . $class->code . '-' . $exam->code . '.pdf');
    }

    private function calculateReportData($students, $results, $exam)
    {
        $data = [];
        
        foreach ($students as $student) {
            $studentResults = $results->get($student->id, collect());
            $totalScore = $studentResults->sum('total_score');
            $subjectCount = $studentResults->count();
            $averageScore = $subjectCount > 0 ? $totalScore / $subjectCount : 0;
            
            $data[$student->id] = [
                'student' => $student,
                'total_score' => $totalScore,
                'average' => $averageScore,
                'subject_count' => $subjectCount,
                'results' => $studentResults,
                'grade' => GradeScale::getGrade($averageScore),
            ];
        }

        return $data;
    }

    private function calculateStudentSummary($results, $exam, $student)
    {
        $totalScore = $results->sum('total_score');
        $subjectCount = $results->count();
        $average = $subjectCount > 0 ? $totalScore / $subjectCount : 0;
        $grade = GradeScale::getGrade($average);
        
        return [
            'total_score' => $totalScore,
            'average' => $average,
            'subject_count' => $subjectCount,
            'grade' => $grade,
            'total_subjects' => $subjectCount,
            'sessional_total' => $totalScore,
            'sessional_average' => $average,
            'total_marks' => $subjectCount * 100,
        ];
    }

    public function buildStudentReportCardData($exam, $student, bool $publishedOnly = true)
    {
        $results = Result::with('subject')
            ->where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->when($publishedOnly, fn ($query) => $query->where('is_published', true))
            ->get();

        $summary = $this->calculateStudentSummary($results, $exam, $student);
        $position = $this->getStudentPosition($exam->id, $student->class_id, $student->id, $publishedOnly);
        $totalStudents = Result::where('exam_id', $exam->id)
            ->where('class_id', $student->class_id)
            ->when($publishedOnly, fn ($query) => $query->where('is_published', true))
            ->distinct('student_id')
            ->count('student_id');

        $subjects = $student->subjects()->where('is_active', true)->orderBy('name')->get();
        $subjectData = $this->buildSubjectData($results, $subjects, $exam->id, $student->class_id, $student->id, $publishedOnly);

        $pin = optional(\App\Models\ResultCheckerPin::where('student_id', $student->id)->where('exam_id', $exam->id)->first())->pin;
        $verificationUrl = $pin && \Illuminate\Support\Facades\Route::has('result-checker.qr')
            ? route('result-checker.qr', ['registration_number' => $student->admission_number, 'pin' => $pin ?: ''])
            : url('/result-checker');
        $qr = (new Builder(
            writer: new PngWriter(),
            data: $verificationUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 180,
            margin: 4,
        ))->build();

        return [
            'exam' => $exam,
            'student' => $student,
            'results' => $results,
            'summary' => $summary,
            'position' => $position,
            'totalStudents' => $totalStudents,
            'subjectData' => $subjectData,
            'schoolLogo' => $this->imageData(getSchoolLogo()),
            'photo' => $this->studentPhotoData($student),
            'qr' => 'data:image/png;base64,' . base64_encode($qr->getString()),
        ];
    }

    private function studentPhotoData($student): ?string
    {
        return $this->imageData($student->photo);
    }

    private function imageData(?string $image): ?string
    {
        if (!$image) {
            return null;
        }

        if (str_starts_with($image, 'data:image/')) {
            return $image;
        }

        $relativePath = parse_url($image, PHP_URL_PATH) ?: $image;
        $relativePath = ltrim(str_replace('/storage/', '', $relativePath), '/');
        $paths = [
            public_path($relativePath),
            public_path('storage/' . $relativePath),
            storage_path('app/public/' . $relativePath),
        ];

        foreach (array_unique($paths) as $path) {
            if (is_file($path)) {
                $mime = mime_content_type($path) ?: 'image/jpeg';
                return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
            }
        }

        return null;
    }

    private function getStudentPosition($examId, $classId, $studentId, bool $publishedOnly = true)
    {
        $totals = Result::where('exam_id', $examId)
            ->where('class_id', $classId)
            ->when($publishedOnly, fn ($query) => $query->where('is_published', true))
            ->get()
            ->groupBy('student_id')
            ->map(fn ($studentResults) => $studentResults->sum('total_score'))
            ->sortByDesc(fn ($score) => $score);

        $rank = 1;
        $position = 1;
        $previousScore = null;

        foreach ($totals as $id => $score) {
            if ($previousScore !== null && $score < $previousScore) {
                $rank = $position;
            }
            if ((int) $id === (int) $studentId) {
                return $rank;
            }
            $previousScore = $score;
            $position++;
        }

        return 'N/A';
    }

    private function buildSubjectData($results, $subjects, $examId, $classId, $studentId, bool $publishedOnly = true)
    {
        $subjectData = [];

        if ($subjects->isEmpty()) {
            $subjects = $results
                ->pluck('subject')
                ->filter()
                ->unique('id')
                ->sortBy('name')
                ->values();
        }

        foreach ($subjects as $subject) {
            $result = $results->firstWhere('subject_id', $subject->id);

            $subjectData[] = [
                'subject' => $subject,
                'ca1' => $result?->ca1_score ?? 0,
                'ca2' => $result?->ca2_score ?? 0,
                'ca3' => $result?->ca3_score ?? 0,
                'total_ca' => ($result?->ca1_score ?? 0) + ($result?->ca2_score ?? 0) + ($result?->ca3_score ?? 0),
                'exam_score' => $result?->exam_score ?? 0,
                'total' => $result?->total_score ?? 0,
                'grade' => $result?->grade ?? 'N/A',
                'remark' => $result?->remark ?? 'N/A',
                'last_term_score' => 0,
                'position' => $this->getSubjectPosition($examId, $classId, $subject->id, $studentId, $publishedOnly),
            ];
        }

        return $subjectData;
    }

    private function getSubjectPosition($examId, $classId, $subjectId, $studentId, bool $publishedOnly = true)
    {
        $scores = Result::where('exam_id', $examId)
            ->where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->when($publishedOnly, fn ($query) => $query->where('is_published', true))
            ->get()
            ->sortByDesc('total_score');

        $rank = 1;
        $position = 1;
        $previousScore = null;

        foreach ($scores as $score) {
            if ($previousScore !== null && $score->total_score < $previousScore) {
                $rank = $position;
            }
            if ((int) $score->student_id === (int) $studentId) {
                return $rank;
            }
            $previousScore = $score->total_score;
            $position++;
        }
        return 'N/A';
    }

    // Publish a single student's report card (creates record if missing)
    public function publish(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        $class = ClassModel::findOrFail($request->class_id);

        if (auth()->check() && auth()->user()->role === 'teacher' && $class->class_teacher_id !== getCurrentEmployeeId()) {
            return redirect()->back()->with('error', 'You can only publish report cards for your own class.');
        }

        $students = Student::where('class_id', $class->id)
            ->where('is_active', true)
            ->get();

        $resultCount = Result::where('exam_id', $request->exam_id)
            ->where('class_id', $request->class_id)
            ->count();

        if ($students->isEmpty() || $resultCount === 0) {
            return redirect()->back()->with('error', 'Cannot publish: no students or results were found for the selected class and exam.');
        }

        setReportCardPublished($request->exam_id, $request->class_id, true);

        Result::where('exam_id', $request->exam_id)
            ->where('class_id', $request->class_id)
            ->update(['is_published' => true]);

        foreach ($students as $student) {
            $reportCard = ReportCard::firstOrCreateForExam($student->id, $request->exam_id, [
                'is_published' => true,
                'published_at' => now(),
                'published_by' => auth()->id(),
            ]);

            $reportCard->update([
                'is_published' => true,
                'published_at' => now(),
                'published_by' => auth()->id(),
            ]);
        }

        return redirect()->back()->with('success', 'Report cards published for the selected class and exam.');
    }

    public function unpublish(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        $class = ClassModel::findOrFail($request->class_id);

        if (auth()->check() && auth()->user()->role === 'teacher' && $class->class_teacher_id !== getCurrentEmployeeId()) {
            return redirect()->back()->with('error', 'You can only unpublish report cards for your own class.');
        }

        $students = Student::where('class_id', $class->id)
            ->where('is_active', true)
            ->pluck('id');

        setReportCardPublished($request->exam_id, $request->class_id, false);

        Result::where('exam_id', $request->exam_id)
            ->where('class_id', $request->class_id)
            ->update(['is_published' => false]);

        ReportCard::where('exam_id', $request->exam_id)
            ->whereIn('student_id', $students)
            ->update([
                'is_published' => false,
                'published_at' => null,
                'published_by' => null,
            ]);

        return redirect()->back()->with('success', 'Report cards unpublished for the selected class and exam.');
    }

    public function bulkPublish(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'exam_id' => 'required|exists:exams,id',
        ]);

        setReportCardPublished($request->exam_id, $request->class_id, true);
        Result::where('exam_id', $request->exam_id)
            ->where('class_id', $request->class_id)
            ->update(['is_published' => true]);

        return redirect()->back()->with('success', 'Report cards published for the selected class and exam.');
    }

    public function bulkUnpublish(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'exam_id' => 'required|exists:exams,id',
        ]);

        setReportCardPublished($request->exam_id, $request->class_id, false);
        Result::where('exam_id', $request->exam_id)
            ->where('class_id', $request->class_id)
            ->update(['is_published' => false]);

        return redirect()->back()->with('success', 'Report cards unpublished for the selected class and exam.');
    }
}
