<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Secondary School Admission Letter · SMS</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: #e9edf4;
      font-family: 'Segoe UI', Roboto, system-ui, -apple-system, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 2rem 1rem;
    }

    .letter-card {
      max-width: 1000px;
      width: 100%;
      background: #ffffff;
      border-radius: 36px;
      box-shadow: 0 25px 45px -10px rgba(0, 20, 30, 0.25);
      padding: 2rem 2.5rem 2rem 2.5rem;
      transition: 0.2s;
      border: 1px solid rgba(255, 255, 255, 0.4);
    }

    .school-header {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 1.2rem 1.8rem;
      padding-bottom: 1.5rem;
      border-bottom: 3px solid #0b2a4a;
      margin-bottom: 1.8rem;
    }

    .header-left {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .logo-box {
      background: #0b2a4a;
      width: 72px;
      height: 72px;
      border-radius: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 2.6rem;
      box-shadow: 0 6px 12px rgba(11, 42, 74, 0.25);
      flex-shrink: 0;
    }

    .school-title {
      line-height: 1.2;
    }

    .school-title h1 {
      font-size: 1.8rem;
      font-weight: 700;
      color: #0b2a4a;
      letter-spacing: -0.3px;
    }

    .school-title .motto {
      font-size: 0.8rem;
      font-weight: 500;
      color: #2a547a;
      background: #eef4fa;
      padding: 0.2rem 1.2rem;
      border-radius: 30px;
      display: inline-block;
      margin-top: 2px;
      letter-spacing: 0.2px;
    }

    .header-right {
      display: flex;
      flex-wrap: wrap;
      gap: 0.6rem 1.8rem;
      background: #f6faff;
      padding: 0.6rem 1.2rem;
      border-radius: 40px;
      border: 1px solid #dde6f2;
      font-size: 0.85rem;
      color: #1d3f60;
    }

    .contact-item {
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .contact-item i {
      color: #0b2a4a;
      width: 1.1rem;
      font-size: 0.9rem;
    }

    .contact-item a {
      color: #0b2a4a;
      text-decoration: none;
      font-weight: 500;
      border-bottom: 1px dotted transparent;
      transition: 0.1s;
    }

    .contact-item a:hover {
      border-bottom: 1px dotted #0b2a4a;
    }

    .letter-meta {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      background: #f9fcff;
      padding: 0.7rem 1.5rem;
      border-radius: 60px;
      border: 1px solid #e6eef9;
      margin-bottom: 1.8rem;
      font-size: 0.9rem;
      color: #1a3f62;
    }

    .meta-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .meta-item i {
      color: #1f5a8e;
    }

    .student-panel {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 1.8rem 2.5rem;
      background: #f2f9ff;
      padding: 1.2rem 2rem;
      border-radius: 30px;
      margin-bottom: 2rem;
      border: 1px solid #d7e5f5;
    }

    .student-avatar {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .avatar-placeholder {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: #0b2a4a;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 2.6rem;
      font-weight: 500;
      box-shadow: 0 6px 12px rgba(11, 42, 74, 0.2);
      border: 3px solid white;
      flex-shrink: 0;
      overflow: hidden;
    }

    .avatar-placeholder img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .student-name-block {
      display: flex;
      flex-direction: column;
    }

    .student-name-block .name {
      font-size: 1.5rem;
      font-weight: 700;
      color: #0b2a4a;
      line-height: 1.2;
    }

    .student-name-block .details {
      font-size: 0.9rem;
      color: #2a547a;
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem 1.2rem;
      margin-top: 0.2rem;
    }

    .student-name-block .details i {
      margin-right: 4px;
      color: #1f5a8e;
    }

    .program-badge {
      margin-left: auto;
      background: #0b2a4a;
      color: white;
      padding: 0.5rem 1.8rem;
      border-radius: 60px;
      font-weight: 600;
      font-size: 0.9rem;
      letter-spacing: 0.4px;
      box-shadow: 0 4px 10px rgba(11, 42, 74, 0.25);
      white-space: nowrap;
    }

    .program-badge i {
      margin-right: 6px;
    }

    .letter-subject {
      font-size: 1rem;
      font-weight: 600;
      color: #0b2a4a;
      background: #e5effa;
      padding: 0.3rem 1.5rem;
      border-radius: 40px;
      display: inline-block;
      margin-bottom: 1.2rem;
    }

    .greeting {
      font-size: 1.05rem;
      font-weight: 500;
      color: #1c3f62;
      margin-bottom: 0.4rem;
    }

    .greeting span {
      font-weight: 700;
      color: #0b2a4a;
    }

    .letter-body {
      color: #1e3144;
      line-height: 1.8;
      font-size: 1rem;
    }

    .letter-body p {
      margin-bottom: 1.2rem;
    }

    .letter-body strong {
      color: #0b2a4a;
    }

    .highlight-box {
      background: #f2f8ff;
      padding: 1rem 1.8rem;
      border-radius: 24px;
      border-left: 6px solid #0b2a4a;
      margin: 1.4rem 0 1.8rem;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      gap: 0.8rem;
    }

    .highlight-box .info-line {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem 1.8rem;
    }

    .highlight-box .info-item {
      display: flex;
      align-items: baseline;
      gap: 0.3rem;
    }

    .highlight-box .info-item .label {
      font-weight: 600;
      color: #1a3e6f;
    }

    .highlight-box .info-item .value {
      background: white;
      padding: 0.1rem 1rem;
      border-radius: 30px;
      border: 1px solid #d0dfef;
      font-weight: 500;
    }

    .signature-area {
      margin-top: 2.8rem;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: end;
      border-top: 2px solid #e2ebf5;
      padding-top: 2rem;
    }

    .signature-block {
      display: flex;
      flex-direction: column;
      gap: 0.2rem;
    }

    .signature-line {
      font-weight: 300;
      color: #2a4a6a;
    }

    .signature-name {
      font-weight: 700;
      font-size: 1.1rem;
      color: #0b2a4a;
    }

    .signature-title {
      font-size: 0.85rem;
      color: #3f6282;
    }

    .school-seal {
      background: #eef4fa;
      border-radius: 60px;
      padding: 0.5rem 1.8rem;
      font-weight: 600;
      color: #0b2a4a;
      border: 1px dashed #8aa9cc;
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 0.85rem;
    }

    .school-seal i {
      font-size: 1.2rem;
    }

    .letter-footer {
      margin-top: 2rem;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      font-size: 0.8rem;
      color: #4d6f8f;
      border-top: 1px solid #e4ecf5;
      padding-top: 1.2rem;
    }

    .footer-left i {
      margin-right: 4px;
      color: #1f5a8e;
    }

    .footer-right a {
      color: #0b2a4a;
      text-decoration: none;
      font-weight: 500;
      margin-left: 0.8rem;
      border-bottom: 1px dotted transparent;
    }

    .footer-right a:hover {
      border-bottom: 1px dotted #0b2a4a;
    }

    @media (max-width: 800px) {
      .letter-card { padding: 1.5rem; }
      .school-header { flex-direction: column; align-items: flex-start; }
      .header-right { width: 100%; justify-content: flex-start; flex-wrap: wrap; }
      .program-badge { margin-left: 0; align-self: flex-start; }
      .student-panel { flex-direction: column; align-items: flex-start; }
      .signature-area { flex-direction: column; align-items: flex-start; gap: 1rem; }
      .letter-meta { flex-direction: column; gap: 0.3rem; align-items: flex-start; border-radius: 20px; }
    }

    @media (max-width: 500px) {
      .header-left { flex-wrap: wrap; }
      .school-title h1 { font-size: 1.4rem; }
      .avatar-placeholder { width: 64px; height: 64px; font-size: 2rem; }
      .student-name-block .name { font-size: 1.2rem; }
    }

    @media print {
      body { background: white; padding: 0.3rem; }
      .letter-card { box-shadow: none; border: 1px solid #ccc; }
      .school-seal { background: #f0f0f0; }
    }
  </style>
</head>
<body>
  @php
    $schoolName = getSchoolName();
    $schoolTagline = getSchoolTagline();
    $schoolAddress = getSchoolAddress();
    $schoolPhone = getSchoolPhone();
    $schoolEmail = getSchoolEmail();
    $schoolWebsite = getSchoolWebsite();
    $schoolLogo = getSchoolLogo();
    $schoolLogoUrl = $schoolLogo
        ? (filter_var($schoolLogo, FILTER_VALIDATE_URL) ? $schoolLogo : asset($schoolLogo))
        : null;
    $student = $application->student;
    $studentPhoto = optional($student)->photo ?: $application->passport_photo;
    $studentPhotoUrl = null;
    if ($studentPhoto) {
        $studentPhotoUrl = filter_var($studentPhoto, FILTER_VALIDATE_URL)
            ? $studentPhoto
            : asset('storage/' . ltrim($studentPhoto, '/'));
    }
    $admissionDate = $application->admitted_at ?? now();
    $admissionYear = $admissionDate->format('Y');
    $nextYear = $admissionDate->copy()->addYear()->format('Y');
    $className = optional($application->class)->full_class_name
        ?? optional($application->class)->name
        ?? $application->applied_class
        ?? 'N/A';
    $studentId = $application->student_id ?? $application->admission_number;
    $termLabel = $admissionYear . '–' . $nextYear;
    $houseName = $application->house ?? optional($student)->house ?? 'N/A';
    $qrVerifyUrl = route('application.admission-letter', $application->admission_number);
    $qrCodeUrl = 'https://chart.googleapis.com/chart?cht=qr&chs=180x180&chl=' . urlencode($qrVerifyUrl) . '&chld=L|2';
    $qrCodeDataUri = null;
    if (function_exists('file_get_contents')) {
        try {
            $qrPayload = @file_get_contents($qrCodeUrl);
            if ($qrPayload) {
                $qrCodeDataUri = 'data:image/png;base64,' . base64_encode($qrPayload);
            }
        } catch (\Throwable $e) {
            $qrCodeDataUri = null;
        }
    }
    $qrCodeSrc = $qrCodeDataUri ?? $qrCodeUrl;
    $signatoryName = getSetting('admission_letter_signatory_name', 'MR. MAXWELL EPHRAIM HALILU');
    $signatoryTitle = getSetting('admission_letter_signatory_title', 'Principal');
    $signatorySignature = getSetting('admission_letter_signatory_signature', '');
    $signatorySignatureUrl = $signatorySignature
        ? (filter_var($signatorySignature, FILTER_VALIDATE_URL) ? $signatorySignature : asset($signatorySignature))
        : null;

    $admissionLetterContent = getSetting('admission_letter_content', '');
    if (!trim($admissionLetterContent)) {
        $admissionLetterContent = <<<'HTML'
<p>We are pleased to inform you that the Admissions Board of <strong>{{ $schoolName }}</strong> has carefully reviewed your application, academic records, and entrance assessment. On behalf of the entire faculty and administration, I am delighted to offer you admission to <strong>{{ $className }}</strong> for the academic year <strong>{{ $termLabel }}</strong>.</p>
<p>Your performance in the entrance examination and interview demonstrated strong analytical skills, intellectual curiosity, and a commitment to community service — values that are at the core of our school’s mission. You have been selected among a competitive pool of applicants, and we believe you will thrive in our rigorous academic environment.</p>
<p>Enclosed with this letter, you will find the enrollment package, which includes the school calendar, uniform code, student handbook, and medical forms. Please complete the online registration and submit the required documents by <strong>August 1, {{ $admissionYear }}</strong> to secure your place.</p>
<p>We also invite you to our <strong>New Student Orientation</strong> on <strong>August 20, {{ $admissionYear }}</strong> at 9:00 AM, where you will meet your teachers, classmates, and House mentor. This is a wonderful opportunity to get acquainted with our campus and settle into school life.</p>
<p>Should you have any questions, our Admissions Office is here to support you. You may reach us at <strong>{{ $schoolPhone ?: 'N/A' }}</strong> or <strong>{{ $schoolEmail ?: 'N/A' }}</strong>.</p>
<p>We are excited to welcome you to the {{ $schoolName }} family and look forward to watching you grow into a confident, compassionate, and accomplished young leader.</p>
HTML;
    }
    $admissionLetterContent = str_replace(
        ['[STUDENT_NAME]', '[CLASS_NAME]', '[TERM_LABEL]', '[SCHOOL_NAME]', '[SCHOOL_PHONE]', '[SCHOOL_EMAIL]', '[ADMISSION_DATE]', '[ADMISSION_NUMBER]'],
        [$application->full_name, $className, $termLabel, $schoolName, $schoolPhone, $schoolEmail, $admissionDate->format('F j, Y'), $application->admission_number],
        $admissionLetterContent
    );
  @endphp

  <div class="letter-card">
    <div class="school-header">
      <div class="header-left">
        <div class="logo-box">
          @if($schoolLogoUrl)
            <img src="{{ $schoolLogoUrl }}" alt="{{ $schoolName }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
          @else
            {{ strtoupper(substr($schoolName, 0, 1) ?: 'S') }}
          @endif
        </div>
        <div class="school-title">
          <h1>{{ $schoolName }}</h1>
          <span class="motto">{{ $schoolTagline }}</span>
        </div>
      </div>
      <div class="header-right">
        @if($schoolWebsite)
          <span class="contact-item"><i>🌐</i> <a href="{{ preg_match('/^https?:\/\//i', $schoolWebsite) ? $schoolWebsite : 'https://' . $schoolWebsite }}" target="_blank">{{ $schoolWebsite }}</a></span>
        @endif
        @if($schoolEmail)
          <span class="contact-item"><i>✉️</i> <a href="mailto:{{ $schoolEmail }}">{{ $schoolEmail }}</a></span>
        @endif
        @if($schoolPhone)
          <span class="contact-item"><i>📞</i> {{ $schoolPhone }}</span>
        @endif
        @if($schoolAddress)
          <span class="contact-item"><i>📍</i> {{ $schoolAddress }}</span>
        @endif
      </div>
    </div>

    <div class="letter-meta">
      <div class="meta-item"><i>📅</i> <strong>Date:</strong> {{ $admissionDate->format('F j, Y') }}</div>
      <div class="meta-item"><i>📝</i> <strong>Ref:</strong> {{ $application->admission_number }}</div>
      <div class="meta-item"><i>🆔</i> <strong>Application:</strong> {{ $application->application_number ?? $application->admission_number }}</div>
    </div>

    <div class="student-panel">
      <div class="student-avatar">
        <div class="avatar-placeholder">
          @if($studentPhotoUrl)
            <img src="{{ $studentPhotoUrl }}" alt="{{ $application->full_name }}">
          @else
            {{ strtoupper(substr($application->first_name, 0, 1) . ($application->last_name ? substr($application->last_name, 0, 1) : '')) }}
          @endif
        </div>
        <div class="student-name-block">
          <span class="name">{{ $application->full_name }}</span>
          <div class="details">
            <span><i>📅</i> DOB: {{ optional($application->date_of_birth)->format('Y-m-d') ?? 'N/A' }}</span>
            <span><i>🆔</i> Student ID: {{ $studentId }}</span>
            <span><i>🎓</i> {{ $className }}</span>
          </div>
        </div>
      </div>
      <div class="program-badge">Secondary · {{ $className }}</div>
    </div>

    <div class="letter-subject">Offer of Admission – Secondary School</div>

    <div class="greeting">
      Dear <span>{{ $application->first_name }}</span>,
    </div>

    <div class="letter-body">
      {!! $admissionLetterContent !!}

      <div class="highlight-box">
        <div class="info-line">
          <span class="info-item"><span class="label">Term:</span> <span class="value">{{ $termLabel }}</span></span>
          <span class="info-item"><span class="label">Start:</span> <span class="value">August 23, {{ $admissionYear }}</span></span>
          <span class="info-item"><span class="label">House:</span> <span class="value">{{ $houseName }}</span></span>
        </div>
        <div style="font-weight: 500; color: #0b2a4a; background: white; padding: 0.2rem 1.2rem; border-radius: 30px; border: 1px solid #c0d7ed;">
          Admission confirmed
        </div>
      </div>

      <p>
        Enclosed with this letter, you will find the enrollment package, which includes the school calendar, uniform code, student handbook, and medical forms. Please complete the online registration and submit the required documents by <strong>August 1, {{ $admissionYear }}</strong> to secure your place.
      </p>
      <p>
        We also invite you to our <strong>New Student Orientation</strong> on <strong>August 20, {{ $admissionYear }}</strong> at 9:00 AM, where you will meet your teachers, classmates, and House mentor. This is a wonderful opportunity to get acquainted with our campus and settle into school life.
      </p>
      <p>
        Should you have any questions, our Admissions Office is here to support you. You may reach us at <strong>{{ $schoolPhone ?: 'N/A' }}</strong> or <strong>{{ $schoolEmail ?: 'N/A' }}</strong>.
      </p>
      <p>
        We are excited to welcome you to the {{ $schoolName }} family and look forward to watching you grow into a confident, compassionate, and accomplished young leader.
      </p>
    </div>

    <div class="signature-area">
      <div class="signature-block">
        <div class="signature-line">Yours sincerely,</div>
        <div class="signature-name">{{ $signatoryName }}</div>
        <div class="signature-title">{{ $signatoryTitle }} · {{ $schoolName }}</div>
        @if($signatorySignatureUrl)
            <img src="{{ $signatorySignatureUrl }}" alt="Signature of {{ $signatoryName }}" style="max-width: 220px; max-height: 100px; margin-top: 0.75rem; object-fit: contain; display:block;">
        @endif
        <div style="margin-top: 6px; font-size: 0.8rem; color: #3f6282;">
          Issued: {{ $admissionDate->format('F j, Y') }}
        </div>
      </div>
      <div class="school-seal">
        <img src="{{ $qrCodeSrc }}" alt="Admission Letter Verification QR" style="width: 80px; height: 80px; object-fit: cover; border-radius: 16px; background: white;">
        <span style="font-size: 0.85rem; display: block; text-align: center;">Scan to verify</span>
      </div>
    </div>

    <div class="letter-footer">
      <div class="footer-left">
        @if($schoolAddress)
          <i>📍</i> {{ $schoolAddress }}
        @endif
        @if($schoolWebsite)
          <span style="margin: 0 0.5rem;">|</span>
          <i>🌐</i> <a href="{{ preg_match('/^https?:\/\//i', $schoolWebsite) ? $schoolWebsite : 'https://' . $schoolWebsite }}" target="_blank">{{ $schoolWebsite }}</a>
        @endif
        @if($schoolEmail)
          <span style="margin: 0 0.5rem;">|</span>
          <i>✉️</i> {{ $schoolEmail }}
        @endif
      </div>
      <div class="footer-right">
        @if($schoolPhone)
          <span><i>📞</i> {{ $schoolPhone }}</span>
        @endif
      </div>
    </div>

    <div style="margin-top: 1rem; font-size: 0.7rem; color: #8da3bb; text-align: center; border-top: 1px solid #e6eef9; padding-top: 0.6rem;">
      {{ $schoolName }} · admission letter · secondary school template
    </div>
  </div>
</body>
</html>
