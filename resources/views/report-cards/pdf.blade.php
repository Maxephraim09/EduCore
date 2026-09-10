<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card - {{ $student->full_name }}</title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #111827; background: white; font-size: 10px; }
        .page { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 12px; border-bottom: 2px solid #1d4ed8; padding-bottom: 8px; }
        .header h1 { margin-bottom: 0; font-size: 24px; }
        .header p { margin: 2px 0; font-size: 12px; }
        .info-grid { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
        .info-grid td { padding: 5px 6px; vertical-align: middle; font-size: 10px; border-bottom: 1px solid #e5e7eb; }
        .info-grid .label { font-weight: bold; width: 18%; color: #374151; }
        .results-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .results-table th, .results-table td { border: 1px solid #d1d5db; padding: 5px 4px; font-size: 9px; text-align: center; }
        .results-table th { background: #1d4ed8; color: white; }
        .results-table td:nth-child(2) { text-align: left; }
        .summary-grid { width: 100%; border-collapse: collapse; }
        .summary-card { background: #eff6ff; padding: 8px; border: 1px solid #bfdbfe; font-size: 10px; width: 33%; }
        .summary-card p { margin: 4px 0 0; font-size: 13px; font-weight: bold; }
        .signature-grid { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .signature-block { width: 30%; text-align: center; font-size: 12px; }
        .signature-line { margin-top: 40px; border-top: 1px solid #000; padding-top: 8px; }
        .small-text { font-size: 10px; color: #555; }
        .student-photo { width: 80px; height: 80px; object-fit: cover; border: 1px solid #9ca3af; }
        .qr { width: 80px; height: 80px; }
        .key-grid, .assessment-grid, .remarks-grid { width: 100%; border-collapse: collapse; margin: 4px 0 10px; }
        .key-grid td, .assessment-grid td, .remarks-grid td { padding: 6px; background: #f8fafc; border: 1px solid #e5e7eb; font-size: 9px; }
        .section-title { font-size: 10px; font-weight: bold; color: #1d4ed8; margin: 8px 0 4px; }
        .key-swatch { display: inline-block; width: 12px; height: 7px; margin-right: 4px; border: 1px solid #9ca3af; }
        .assessment-grid strong, .remarks-grid strong { display: block; color: #374151; margin-bottom: 3px; }
        .signature-grid .signature-line { height: 22px; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            @php
                $schoolName = getSetting('school_name', 'Excellence International School');
                $schoolAddress = getSetting('school_address', '');
                $schoolEmail = getSetting('school_email', 'info@school.edu.ng');
                $schoolWebsite = getSetting('school_website', '');
            @endphp
            @if(!empty($schoolLogo))
                <p><img src="{{ $schoolLogo }}" alt="{{ $schoolName }}" style="max-height:70px;max-width:180px;margin-bottom:6px;"></p>
            @endif
            <h1>{{ $schoolName }}</h1>
            @if($schoolAddress)
                <p>{{ $schoolAddress }}</p>
            @endif
            <p>Email: {{ $schoolEmail }} {{ $schoolWebsite ? '| Website: ' . $schoolWebsite : '' }}</p>
            <p class="small-text">Academic Session: {{ $exam->academic_year }} | Term: {{ $exam->term }}</p>
            <p class="small-text">Report Card for: {{ $student->full_name }}</p>
        </div>

        <table class="info-grid">
            <tr><td colspan="2">@if(!empty($photo))<img class="student-photo" src="{{ $photo }}" alt="Student photo">@endif</td><td colspan="2" style="text-align:right">@if(!empty($qr))<img class="qr" src="{{ $qr }}" alt="Verification QR code"><br><span class="small-text">Scan to verify</span>@endif</td></tr>
            <tr>
                <td class="label">Admission Number</td>
                <td>{{ $student->admission_number }}</td>
                <td class="label">Class</td>
                <td>{{ $student->class->full_class_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Date of Birth</td>
                <td>{{ $student->date_of_birth ? date('F d, Y', strtotime($student->date_of_birth)) : 'N/A' }}</td>
                <td class="label">Gender</td>
                <td>{{ $student->gender ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Average Score</td>
                <td>{{ number_format($summary['average'], 2) }}%</td>
                <td class="label">Class Position</td>
                <td>{{ $position }} / {{ $totalStudents }}</td>
            </tr>
            <tr><td class="label">Total Marks</td><td>{{ $summary['total_score'] }} / {{ $summary['total_marks'] }}</td><td class="label">Sessional Position</td><td>{{ $position }} / {{ $totalStudents }}</td></tr>
            <tr><td class="label">Sessional Total</td><td>{{ $summary['sessional_total'] }}</td><td class="label">Sessional Average</td><td>{{ number_format($summary['sessional_average'], 2) }}%</td></tr>
        </table>

        <div class="section-title">Grading Key</div>
        <table class="key-grid"><tr>
            @foreach(\App\Models\GradeScale::where('is_active', true)->orderBy('min_score', 'desc')->get() as $grade)
                <td><span class="key-swatch" style="background: {{ $grade->color ?? '#dbeafe' }}"></span>{{ $grade->grade }}: {{ $grade->min_score }}-{{ $grade->max_score }} ({{ $grade->remark }})</td>
            @endforeach
            <td><strong>Highest:</strong> {{ $results->max('total_score') ?? 0 }} | <strong>Lowest:</strong> {{ $results->min('total_score') ?? 0 }}</td>
        </tr></table>

        <table class="results-table">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Subject</th>
                    <th>CA1</th>
                    <th>CA2</th>
                    <th>CA3</th>
                    <th>Total CA</th>
                    <th>Exam</th>
                    <th>Total</th>
                    <th>Position</th>
                    <th>Grade</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subjectData as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data['subject']->name }}</td>
                    <td>{{ $data['ca1'] }}</td>
                    <td>{{ $data['ca2'] }}</td>
                    <td>{{ $data['ca3'] }}</td>
                    <td>{{ $data['total_ca'] }}</td>
                    <td>{{ $data['exam_score'] }}</td>
                    <td>{{ $data['total'] }}</td>
                    <td>{{ $data['position'] }}</td>
                    <td>{{ $data['grade'] }}</td>
                    <td>{{ $data['remark'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="section-title">Character, Conduct and Skills</div>
        <table class="assessment-grid"><tr>
            <td><strong>Character &amp; Conduct</strong>Punctuality: Good<br>Attendance: Good<br>Neatness: Good<br>Participation: Good</td>
            <td><strong>Skills &amp; Competencies</strong>Leadership: Good<br>Teamwork: Good<br>Communication: Good<br>Problem Solving: Good</td>
        </tr></table>

        <table class="summary-grid"><tr>
            <td class="summary-card">
                <strong>Total Score</strong>
                <p>{{ $summary['total_score'] }}</p>
            </td>
            <td class="summary-card">
                <strong>Average</strong>
                <p>{{ number_format($summary['average'], 1) }}%</p>
            </td>
            <td class="summary-card">
                <strong>Overall Grade</strong>
                <p>{{ $summary['grade'] ? $summary['grade']->grade : 'N/A' }}</p>
            </td>
        </tr></table>

        <table class="remarks-grid"><tr>
            <td><strong>Teacher's Remarks</strong>{{ $student->results->firstWhere('exam_id', $exam->id)?->teacher_comment ?? 'Performance has been reviewed for this term.' }}</td>
            <td><strong>Principal's Remarks</strong>{{ $student->results->firstWhere('exam_id', $exam->id)?->principal_comment ?? 'Continue working hard and maintaining good conduct.' }}</td>
        </tr></table>

        <table class="signature-grid"><tr>
            <td class="signature-block">
                <div class="signature-line"></div>
                Class Teacher
            </td>
            <td class="signature-block">
                <div class="signature-line"></div>
                Principal
            </td>
            <td class="signature-block">
                <div class="signature-line"></div>
                Parent / Guardian
            </td>
        </tr></table>

        <p class="small-text">Generated on {{ date('F d, Y') }}</p>
    </div>
</body>
</html>
