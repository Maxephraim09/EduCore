<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Report Cards - {{ $class->full_class_name }} - {{ $exam->term }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; background: white; }
        .page { width: 100%; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 { margin-bottom: 0; font-size: 22px; }
        .header p { margin: 4px 0; font-size: 12px; }
        .results-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .results-table th, .results-table td { border: 1px solid #d1d5db; padding: 8px; font-size: 10px; }
        .results-table th { background: #f3f4f6; }
        .student-block { page-break-after: always; page-break-inside: avoid; margin-bottom: 24px; }
        .student-title { background: #f8fafc; padding: 10px; border: 1px solid #e2e8f0; margin-bottom: 12px; }
        .student-photo { width: 72px; height: 72px; object-fit: cover; float: right; border: 1px solid #9ca3af; }
        .student-summary { width: 100%; border-collapse: collapse; margin: 10px 0 14px; }
        .student-summary td { border: 1px solid #d1d5db; padding: 6px; font-size: 10px; }
        .qr { width: 72px; height: 72px; float: right; margin-top: 8px; }
        .signature-grid { display: flex; justify-content: space-between; margin-top: 24px; }
        .signature-block { width: 30%; text-align: center; font-size: 11px; }
        .signature-line { margin-top: 24px; border-top: 1px solid #000; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            @php
                $schoolName = getSetting('school_name', 'Excellence International School');
                $schoolAddress = getSetting('school_address', '');
                $schoolEmail = getSetting('school_email', 'info@school.edu.ng');
            @endphp
            <h1>{{ $schoolName }}</h1>
            <p>{{ $schoolAddress }}</p>
            <p>Email: {{ $schoolEmail }}</p>
            <p><strong>Class:</strong> {{ $class->full_class_name }} | <strong>Exam:</strong> {{ $exam->term }} ({{ $exam->academic_year }})</p>
            <p><strong>Generated:</strong> {{ date('F d, Y') }}</p>
        </div>

        @foreach($reportCards as $studentData)
            <?php $student = $studentData['student']; ?>
            <div class="student-block">
                <div class="student-title">
                    @if($studentData['photo'])<img class="student-photo" src="{{ $studentData['photo'] }}" alt="Student photo">@endif
                    <strong>{{ $student->full_name }}</strong> — Admission No: {{ $student->admission_number }} | Class: {{ $class->full_class_name }}
                    <br>Position: {{ $studentData['position'] }} / {{ $studentData['totalStudents'] }}
                </div>

                <table class="student-summary">
                    <tr>
                        <td><strong>Total Marks:</strong> {{ $studentData['summary']['total_score'] }} / {{ $studentData['summary']['total_marks'] }}</td>
                        <td><strong>Average:</strong> {{ number_format($studentData['summary']['average'], 2) }}%</td>
                        <td><strong>Sessional Total:</strong> {{ $studentData['summary']['sessional_total'] }}</td>
                        <td><strong>Sessional Average:</strong> {{ number_format($studentData['summary']['sessional_average'], 2) }}%</td>
                    </tr>
                    <tr><td><strong>Sessional Position:</strong> {{ $studentData['position'] }} / {{ $studentData['totalStudents'] }}</td><td colspan="3"><strong>Grade:</strong> {{ $studentData['summary']['grade']?->grade ?? 'N/A' }}</td></tr>
                </table>

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
                            <th>Grade</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentData['subjectData'] as $index => $result)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $result['subject']->name }}</td>
                            <td>{{ $result['ca1'] }}</td>
                            <td>{{ $result['ca2'] }}</td>
                            <td>{{ $result['ca3'] }}</td>
                            <td>{{ $result['total_ca'] }}</td>
                            <td>{{ $result['exam_score'] }}</td>
                            <td>{{ $result['total'] }}</td>
                            <td>{{ $result['grade'] }}</td>
                            <td>{{ $result['remark'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="signature-grid">
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        Class Teacher
                    </div>
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        Principal
                    </div>
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        Parent / Guardian
                    </div>
                </div>
                <img class="qr" src="{{ $studentData['qr'] }}" alt="Result verification QR code">
            </div>
        @endforeach
    </div>
</body>
</html>
