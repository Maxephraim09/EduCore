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
            <h1>Application Received</h1>
        </div>

        <div class="content">
            <p>Dear {{ $application->first_name }},</p>
            <p>Thank you for submitting your application. Your application number is <strong>{{ $application->application_number }}</strong>.</p>
            <p>We have received your application successfully and will review it shortly.</p>
            <p>
                <strong>Applied For:</strong>
                {{ optional($application->class)->full_class_name ?? optional($application->class)->name ?? 'N/A' }}
            </p>
            <p>
                You can check your application status online at:
                <a href="{{ url(route('application.status.form')) }}">{{ url(route('application.status.form')) }}</a>
            </p>
            <p>Best regards,<br>The Admissions Team</p>
        </div>

        <div class="footer">
            <p>Please keep this email for your records.</p>
        </div>
    </div>
</body>
</html>
