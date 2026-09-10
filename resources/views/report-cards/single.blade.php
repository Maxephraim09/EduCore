@extends('layouts.print')

@section('title', 'Report Card - ' . $student->full_name)

@push('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f9;
        }

        .report-container {
            max-width: 1100px;
            width: 100%;
            margin: 0 auto;
            padding: 20px 0;
        }

        .report-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            overflow: hidden;
            width: 100%;
        }

        .school-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 18px 25px;
            position: relative;
            overflow: hidden;
        }

        .school-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1%, transparent 1%);
            background-size: 50px 50px;
            animation: shimmer 20s linear infinite;
        }

        @keyframes shimmer {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .school-logo {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 55px;
            height: 55px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .logo-icon i {
            font-size: 30px;
            color: #2a5298;
        }

        .school-title h1 {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 3px;
        }

        .school-title p {
            font-size: 9px;
            opacity: 0.9;
            line-height: 1.3;
        }

        .academic-badge {
            background: rgba(255,255,255,0.2);
            padding: 6px 15px;
            border-radius: 10px;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .academic-badge .label {
            font-size: 9px;
            opacity: 0.8;
        }

        .academic-badge .value {
            font-size: 14px;
            font-weight: 700;
        }

        .student-profile {
            padding: 15px 25px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 20px;
            align-items: start;
        }

        .student-photo {
            text-align: center;
        }

        .photo-frame {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 5px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .photo-frame i {
            font-size: 45px;
            color: white;
        }

        .photo-placeholder {
            font-size: 9px;
            color: #64748b;
        }

        .student-info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .info-field {
            background: white;
            padding: 8px 10px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .info-field .label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .info-field .value {
            font-size: 11px;
            font-weight: 600;
            color: #1e293b;
        }

        .key-section {
            padding: 8px 25px;
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
        }

        .grading-key {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: space-between;
            align-items: center;
        }

        .key-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .key-color {
            width: 20px;
            height: 10px;
            border-radius: 3px;
        }

        .key-text {
            font-size: 9px;
            font-weight: 500;
        }

        .results-section {
            padding: 15px 25px;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }

        .results-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 4px;
            text-align: center;
            font-weight: 600;
            font-size: 8px;
        }

        .results-table td {
            padding: 5px 4px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            font-size: 8.5px;
        }

        .results-table tr:hover {
            background: #f8fafc;
        }

        .subject-name {
            font-weight: 600;
            color: #1e293b;
            text-align: left;
            font-size: 9px;
        }

        .grade-cell {
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 12px;
            display: inline-block;
            font-size: 8px;
        }

        .grade-A { background: #d1fae5; color: #065f46; }
        .grade-B { background: #dbeafe; color: #1e40af; }
        .grade-C { background: #fed7aa; color: #92400e; }
        .grade-D { background: #fee2e2; color: #991b1b; }
        .grade-F { background: #fecaca; color: #7f1d1d; }

        .performance-summary {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
            margin-bottom: 0;
            padding: 10px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 10px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-label {
            font-size: 8px;
            color: #64748b;
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
        }

        .character-section {
            padding: 0 25px 12px 25px;
        }

        .character-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 10px;
        }

        .character-card {
            background: #f8fafc;
            padding: 10px;
            border-radius: 10px;
            border-left: 3px solid #667eea;
        }

        .character-title {
            font-weight: 700;
            color: #667eea;
            margin-bottom: 8px;
            font-size: 10px;
        }

        .conduct-items {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .conduct-item {
            flex: 1;
            min-width: 70px;
        }

        .conduct-label {
            font-size: 8px;
            color: #64748b;
        }

        .conduct-value {
            font-weight: 600;
            color: #1e293b;
            font-size: 9px;
        }

        .rating-stars {
            color: #fbbf24;
            letter-spacing: 1px;
            font-size: 8px;
        }

        .remarks-section {
            padding: 12px 25px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .remarks-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .remarks-box {
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .remarks-box h4 {
            color: #667eea;
            margin-bottom: 6px;
            font-size: 10px;
        }

        .remarks-text {
            line-height: 1.4;
            color: #475569;
            font-size: 9px;
        }

        .signatures {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .signature-line {
            text-align: center;
        }

        .signature-line .line {
            width: 130px;
            border-top: 1.5px solid #cbd5e1;
            margin-bottom: 5px;
        }

        .signature-line .label {
            font-size: 8px;
            color: #64748b;
        }

        .qr-section {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }

        .qr-code {
            text-align: center;
        }

        .qr-code canvas {
            width: 55px;
            height: 55px;
        }

        .qr-text {
            font-size: 7px;
            color: #64748b;
            margin-top: 2px;
        }

        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            transition: all 0.3s;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
        }

        .print-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .print-btn {
                display: none;
            }

            .report-container {
                max-width: 100%;
                margin: 0;
                padding: 0;
            }

            .report-card {
                box-shadow: none;
                margin: 0;
                border-radius: 0;
                width: 100%;
                max-width: 100%;
                height: auto;
            }

            .school-header::before {
                display: none;
            }

            @page {
                size: A4;
                margin: 0.3cm;
            }

            .school-header,
            .results-table th,
            .grade-cell,
            .key-color,
            .character-card,
            .remarks-box,
            .performance-summary {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .report-card {
                page-break-after: avoid;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .results-table {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .school-header {
                padding: 12px 20px;
            }

            .student-profile {
                padding: 10px 20px;
            }

            .results-section {
                padding: 10px 20px;
            }

            .remarks-section {
                padding: 10px 20px;
            }

            .character-section {
                padding: 0 20px 8px 20px;
            }

            .info-field .value {
                font-size: 10px;
            }

            .summary-value {
                font-size: 12px;
            }
        }

        @media (max-width: 900px) {
            .student-info-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .performance-summary {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 600px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
            .student-photo {
                text-align: center;
            }
            .photo-frame {
                margin: 0 auto;
            }
            .student-info-grid {
                grid-template-columns: 1fr;
            }
            .performance-summary {
                grid-template-columns: repeat(2, 1fr);
            }
            .remarks-grid {
                grid-template-columns: 1fr;
            }
            .character-grid {
                grid-template-columns: 1fr;
            }
            .grading-key {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@php
    $schoolName = getSetting('school_name', 'Excellence International School');
    $schoolAddress = getSetting('school_address', '');
    $schoolEmail = getSetting('school_email', 'info@school.edu.ng');
    $schoolWebsite = getSetting('school_website', '');
    $schoolLogo = getSetting('school_logo_url', '');
@endphp

@section('content')
    <div class="report-container">
        <div class="report-card" id="reportCard">
            <!-- School Header -->
            <div class="school-header">
                <div class="school-logo">
                    <div class="logo-area">
                                <div class="logo-icon">
                            @if($schoolLogo)
                                <img src="{{ filter_var($schoolLogo, FILTER_VALIDATE_URL) ? $schoolLogo : asset($schoolLogo) }}" alt="{{ $schoolName }}" style="max-height: 55px; max-width: 100%; object-fit: contain;">
                            @else
                                <i class="fas fa-graduation-cap"></i>
                            @endif
                        </div>
                        <div class="school-title">
                            <h1>{{ strtoupper($schoolName) }}</h1>
                            @if($schoolAddress)
                                <p>{{ $schoolAddress }}</p>
                            @endif
                            <p>Email: {{ $schoolEmail }}{{ $schoolWebsite ? ' | Website: ' . $schoolWebsite : '' }}</p>
                        </div>
                    </div>
                    <div class="academic-badge">
                        <div class="label">Academic Session</div>
                        <div class="value">{{ $exam->academic_year }}</div>
                        <div class="label">Term</div>
                        <div class="value">{{ $exam->term }}</div>
                    </div>
                </div>
            </div>

            <!-- Student Profile -->
            <div class="student-profile">
                <div class="profile-grid">
                    <div class="student-photo">
                        <div class="photo-frame">
                            @if(!empty($photo))
                                <img src="{{ $photo }}" alt="{{ $student->full_name }}" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
                            @else
                                <i class="fas fa-user-graduate"></i>
                            @endif
                        </div>
                        <div class="photo-placeholder">Student Photo</div>
                    </div>
                    <div class="student-info-grid">
                        <div class="info-field">
                            <div class="label">Student Name</div>
                            <div class="value">{{ strtoupper($student->full_name) }}</div>
                        </div>
                        <div class="info-field">
                            <div class="label">Admission Number</div>
                            <div class="value">{{ $student->admission_number }}</div>
                        </div>
                        <div class="info-field">
                            <div class="label">Class</div>
                            <div class="value">{{ optional($student->class)->full_class_name ?? $student->class_name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-field">
                            <div class="label">Section</div>
                            <div class="value">{{ optional($student->class)->section ?? $student->section ?? 'N/A' }}</div>
                        </div>
                        <div class="info-field">
                            <div class="label">Gender</div>
                            <div class="value">{{ $student->gender ?? 'N/A' }}</div>
                        </div>
                        <div class="info-field">
                            <div class="label">Age</div>
                            <div class="value">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->age . ' years' : 'N/A' }}</div>
                        </div>
                        <div class="info-field">
                            <div class="label">Date of Birth</div>
                            <div class="value">{{ $student->date_of_birth ? date('F d, Y', strtotime($student->date_of_birth)) : 'N/A' }}</div>
                        </div>
                        <div class="info-field">
                            <div class="label">House</div>
                            <div class="value">{{ $student->house ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grading Key -->
            <div class="key-section">
                <div class="grading-key">
                    @php
                        $gradeScales = \App\Models\GradeScale::where('is_active', true)->get();
                    @endphp
                    @foreach($gradeScales as $grade)
                    <div class="key-item">
                        <div class="key-color" style="background: {{ $grade->color ?? '#ccc' }};"></div>
                        <span class="key-text">{{ $grade->grade }}: {{ $grade->min_score }}-{{ $grade->max_score }}% ({{ $grade->remark }})</span>
                    </div>
                    @endforeach
                    <div class="key-item">
                        <i class="fas fa-chart-line"></i>
                        <span class="key-text">Highest: {{ $results->max('total_score') ?? 0 }}% | Lowest: {{ $results->min('total_score') ?? 0 }}%</span>
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="results-section">
                <table class="results-table">
                    <thead>
                        <tr>
                            <th rowspan="2">S/N</th>
                            <th rowspan="2">SUBJECT</th>
                            <th colspan="3">Continuous Assessment</th>
                            <th rowspan="2">Total CA<br>(30)</th>
                            <th rowspan="2">Exam<br>(70)</th>
                            <th rowspan="2">Total<br>(100)</th>
                            <th rowspan="2">Last Term<br>Score</th>
                            <th rowspan="2">Grade</th>
                            <th rowspan="2">Position</th>
                            <th rowspan="2">Remark</th>
                        </tr>
                        <tr>
                            <th>1st</th>
                            <th>2nd</th>
                            <th>3rd</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjectData as $index => $data)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="subject-name">{{ $data['subject']->name }}</td>
                            <td>{{ $data['ca1'] }}</td>
                            <td>{{ $data['ca2'] }}</td>
                            <td>{{ $data['ca3'] }}</td>
                            <td>{{ $data['total_ca'] }}</td>
                            <td>{{ $data['exam_score'] }}</td>
                            <td><strong>{{ $data['total'] }}</strong></td>
                            <td>{{ $data['last_term_score'] ?? 'N/A' }}</td>
                            <td>
                                <span class="grade-cell {{ $data['grade'] != 'N/A' ? 'grade-' . $data['grade'] : '' }}">
                                    {{ $data['grade'] }}
                                </span>
                            </td>
                            <td>{{ $data['position'] }}</td>
                            <td>{{ $data['remark'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Performance Summary -->
                <div class="performance-summary">
                    <div class="summary-item">
                        <div class="summary-label">Total Score</div>
                        <div class="summary-value">{{ $summary['total_score'] }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Average</div>
                        <div class="summary-value">{{ number_format($summary['average'], 1) }}%</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Sessional Total</div>
                        <div class="summary-value">{{ $summary['sessional_total'] }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Sessional Avg</div>
                        <div class="summary-value">{{ number_format($summary['sessional_average'], 2) }}%</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Class Position</div>
                        <div class="summary-value">{{ $position }} / {{ $totalStudents }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Sessional Position</div>
                        <div class="summary-value">{{ $position }} / {{ $totalStudents }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Overall Grade</div>
                        <div class="summary-value">
                            @if($summary['grade'])
                                <span style="color: {{ $summary['grade']->color ?? '#667eea' }};">
                                    {{ $summary['grade']->grade }}
                                </span>
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Character/Conduct Section -->
            <div class="character-section">
                <div class="character-grid">
                    <div class="character-card">
                        <div class="character-title">
                            <i class="fas fa-star"></i> Character & Conduct Assessment
                        </div>
                        <div class="conduct-items">
                            @php
                                $conducts = ['Punctuality', 'Attendance', 'Neatness', 'Attentiveness', 'Participation', 'Respectfulness'];
                                $ratings = ['★★★★★', '★★★★☆', '★★★☆☆', '★★☆☆☆', '★☆☆☆☆'];
                            @endphp
                            @foreach($conducts as $conduct)
                            <div class="conduct-item">
                                <div class="conduct-label">{{ $conduct }}</div>
                                <div class="conduct-value rating-stars">
                                    {{ $ratings[array_rand($ratings)] }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="character-card">
                        <div class="character-title">
                            <i class="fas fa-clipboard-list"></i> Skills & Competencies
                        </div>
                        <div class="conduct-items">
                            @php
                                $skills = ['Leadership', 'Teamwork', 'Communication', 'Problem Solving', 'Creativity', 'Critical Thinking'];
                                $levels = ['Excellent', 'Very Good', 'Good', 'Satisfactory', 'Needs Improvement'];
                            @endphp
                            @foreach($skills as $skill)
                            <div class="conduct-item">
                                <div class="conduct-label">{{ $skill }}</div>
                                <div class="conduct-value">{{ $levels[array_rand($levels)] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remarks and Signatures -->
            <div class="remarks-section">
                <div class="remarks-grid">
                    <div>
                        <div class="remarks-box">
                            <h4><i class="fas fa-comment-dots"></i> Teacher's Remarks</h4>
                            <div class="remarks-text">
                                @php
                                    $teacherRemarks = [
                                        'Has demonstrated exceptional academic prowess this term. Performance in core subjects is outstanding.',
                                        'Shows great enthusiasm for learning and actively participates in class discussions.',
                                        'Excellent performance. Should continue this momentum and pay more attention to challenging subjects.',
                                        'A brilliant and well-behaved student. Keep up the good work.',
                                        'Demonstrates strong analytical skills and critical thinking abilities.'
                                    ];
                                @endphp
                                {{ $teacherRemarks[array_rand($teacherRemarks)] }}
                            </div>
                        </div>
                        <div class="remarks-box" style="margin-top: 8px;">
                            <h4><i class="fas fa-chalkboard-user"></i> Principal's Remarks</h4>
                            <div class="remarks-text">
                                @php
                                    $principalRemarks = [
                                        'A brilliant and well-behaved student. Congratulations on excellent results!',
                                        'Outstanding performance. Keep up the great work.',
                                        'We expect even better performance next term. Congratulations!',
                                        'A model student with excellent academic and character traits.'
                                    ];
                                @endphp
                                {{ $principalRemarks[array_rand($principalRemarks)] }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="signatures">
                            <div class="signature-line">
                                <div class="line"></div>
                                <div class="label">Class Teacher's Signature</div>
                                <div class="label">Mrs. Adeola Williams</div>
                            </div>
                            <div class="signature-line">
                                <div class="line"></div>
                                <div class="label">Principal's Signature</div>
                                <div class="label">Dr. Oluwaseun Adebayo</div>
                            </div>
                            <div class="signature-line">
                                <div class="line"></div>
                                <div class="label">Parent's/Guardian's Signature</div>
                            </div>
                        </div>
                        <div class="qr-section">
                            <div class="qr-code"><img src="{{ $qr }}" alt="Result verification QR code" style="width:55px;height:55px;"></div>
                            <div class="qr-text">
                                <i class="fas fa-qrcode"></i> Scan to verify<br>
                                Result Authenticity
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">
        <i class="fas fa-print"></i> Print / Download PDF
    </button>
@endsection

