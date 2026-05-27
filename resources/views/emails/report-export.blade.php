<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Report</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #374151;
            background: #f9fafb;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            max-width: 600px;
            margin: 32px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: #1d4ed8;
            color: #ffffff;
            padding: 24px 32px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .header p {
            margin: 4px 0 0;
            font-size: 12px;
            opacity: 0.8;
        }

        .body {
            padding: 32px;
        }

        .body p {
            white-space: pre-line;
            margin: 0 0 16px;
        }

        .attachment-notice {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 12px 16px;
            margin-top: 24px;
            color: #1d4ed8;
            font-size: 13px;
        }

        .footer {
            background: #f3f4f6;
            border-top: 1px solid #e5e7eb;
            padding: 16px 32px;
            font-size: 11px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="header">
            <h1>Progress Report</h1>
            <p>Laporan dikirim secara otomatis oleh sistem</p>
        </div>

        <div class="body">
            <p>{{ $body }}</p>

            <div class="attachment-notice">
                &nbsp; File PDF laporan terlampir pada email ini.
            </div>
        </div>

        <div class="footer">
            Email ini dibuat otomatis. Mohon tidak membalas email ini langsung.<br>
            © {{ date('Y') }} — Progress Report System
        </div>
    </div>
</body>

</html>
