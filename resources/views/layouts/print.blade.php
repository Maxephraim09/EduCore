<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Report Card')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #111827;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }
        * {
            box-sizing: border-box;
        }
        .page-container {
            width: 210mm;
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 10mm;
            background: #ffffff;
            box-shadow: 0 0 22px rgba(0,0,0,0.08);
            box-sizing: border-box;
        }
        .no-print {
            display: none;
        }
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        @media print {
            body, html {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
                background: #ffffff;
            }
            .page-container {
                width: 100%;
                max-width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
                overflow: hidden;
            }
            .page-container {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .print-btn {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="page-container">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
