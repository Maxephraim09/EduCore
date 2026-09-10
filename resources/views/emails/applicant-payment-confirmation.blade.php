<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #198754; color: #fff; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f8f9fa; border: 1px solid #e9ecef; }
        .button { display: inline-block; background: #198754; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; }
        .footer { font-size: 0.9rem; color: #6c757d; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Application Fee Payment Confirmed</h1>
        </div>

        <div class="content">
            <p>Dear {{ $applicant->name }},</p>
            <p>We have received your application fee payment.</p>
            <p>
                <strong>Reference:</strong> {{ $transaction->reference }}<br>
                <strong>Amount:</strong> ₦{{ number_format($transaction->amount, 2) }}<br>
                <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $transaction->status)) }}
            </p>
            <p>Your application can now proceed to the next step.</p>
            <p><a class="button" href="{{ url(route('application.portal.index')) }}">Continue to Application Portal</a></p>
            <p>Best regards,<br>The Admissions Team</p>
        </div>

        <div class="footer">
            <p>If you have any questions, please contact admissions support.</p>
        </div>
    </div>
</body>
</html>
