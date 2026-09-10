<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $salary->employee->full_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
        }
        .payslip-title {
            font-size: 18px;
            margin-top: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            width: 200px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 12px;
        }
        .signature {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ getSchoolName() }}</div>
            @if(getSchoolTagline())
                <div class="payslip-title">{{ getSchoolTagline() }}</div>
            @else
                <div class="payslip-title">PAYSLIP</div>
            @endif
        </div>
        
        <div class="info-row">
            <div class="info-label">Employee Name:</div>
            <div class="info-value">{{ $salary->employee->full_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Employee ID:</div>
            <div class="info-value">{{ $salary->employee->employee_id }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Position:</div>
            <div class="info-value">{{ $salary->employee->position }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Department:</div>
            <div class="info-value">{{ $salary->employee->department }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Pay Period:</div>
            <div class="info-value">{{ $salary->month }} {{ $salary->year }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Payment Date:</div>
            <div class="info-value">{{ $salary->payment_date->format('d M Y') }}</div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount (₦)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td class="text-right">{{ number_format($salary->basic_salary, 2) }}</td>
                </tr>
                <tr>
                    <td>Allowances</td>
                    <td class="text-right">{{ number_format($salary->allowances, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Gross Salary</td>
                    <td class="text-right">{{ number_format($salary->basic_salary + $salary->allowances, 2) }}</td>
                </tr>
                <tr>
                    <td>Deductions</td>
                    <td class="text-right">{{ number_format($salary->deductions, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Net Salary</td>
                    <td class="text-right">{{ number_format($salary->net_salary, 2) }}</td>
                </tr>
            </tbody>
        </table>
        
        <div class="info-row">
            <div class="info-label">Payment Method:</div>
            <div class="info-value">{{ ucfirst($salary->payment_method) }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Transaction ID:</div>
            <div class="info-value">{{ $salary->transaction_id }}</div>
        </div>
        
        <div class="signature">
            <div>_____________________<br>Employee Signature</div>
            <div>_____________________<br>Authorized Signature</div>
        </div>
        
        <div class="footer">
            This is a computer-generated payslip. No signature required.<br>
            Generated on: {{ now()->format('d M Y H:i:s') }}
        </div>
    </div>
</body>
</html>