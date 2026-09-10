<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0d6efd; color: #fff; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; border: 1px solid #e9ecef; }
        .button { display: inline-block; background: #0d6efd; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; }
        .footer { font-size: 0.9rem; color: #6c757d; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admission Letter</h1>
        </div>

        <div class="content">
            <p>Dear {{ $application->first_name }},</p>
            <p>Congratulations! Your application has been approved and you have been admitted.</p>
            <p>
                Admission Number: <strong>{{ $application->admission_number }}</strong><br>
                Class: <strong>{{ optional($application->class)->full_class_name ?? optional($application->class)->name ?? 'N/A' }}</strong>
            </p>
            <p>
                Use the following details to login to the student portal:
            </p>
            <ul>
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Password:</strong> {{ $password }}</li>
                <li><strong>Admission Number:</strong> {{ $application->admission_number }}</li>
            </ul>
            <p>You can download your admission letter here:</p>
            <p><a class="button" href="{{ url(route('application.admission-letter.download', $application->admission_number)) }}">Download Admission Letter</a></p>
            <p>Best regards,<br>The Admissions Team</p>
        </div>

        <div class="footer">
            <p>Please keep this email for your records.</p>
        </div>
    </div>
</body>
</html>
