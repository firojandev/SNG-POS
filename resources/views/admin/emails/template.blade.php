<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Email from ' . get_option('app_name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 25px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-header p {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .email-body {
            padding: 25px 20px;
            border-left: 2px solid #667eea;
            border-right: 2px solid #667eea;
        }
        .email-body h2 {
            color: #667eea;
            font-size: 20px;
            margin-bottom: 10px;
        }
        .email-body p {
            margin-bottom: 10px;
            color: #555555;
        }
        .email-body ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .email-body ul li {
            margin-bottom: 5px;
            color: #555555;
        }
        .email-body strong {
            color: #333333;
        }
        .email-footer {
            background-color: #667eea;
            padding: 15px 20px;
            text-align: center;
            color: white;
        }
        .email-footer p {
            margin: 4px 0;
            font-size: 13px;
        }
        .email-footer a {
            text-decoration: none;
        }
        .email-footer a:hover {
            text-decoration: underline;
        }
        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 15px 0;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                border-radius: 0;
            }
            .email-header {
                padding: 20px 15px;
            }
            .email-header h1 {
                font-size: 20px;
            }
            .email-body {
                padding: 20px 15px;
                border-left: 3px solid #667eea;
                border-right: 3px solid #667eea;
            }
            .email-footer {
                padding: 12px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Email Header -->
        <div class="email-header">
            <h1>{{ get_option('app_name', 'SNG POS') }}</h1>
            @if(get_option('app_phone'))
                <p>{{ get_option('app_phone') }}</p>
            @endif
        </div>

        <!-- Email Body -->
        <div class="email-body">
            {!! $body !!}
        </div>

        <!-- Email Footer -->
        <div class="email-footer">
            <p><strong>{{ get_option('app_name', 'SNG POS') }}</strong></p>
            @if(get_option('app_address'))
                <p>{{ get_option('app_address') }}</p>
            @endif
            <p style="margin-top: 10px; font-size: 12px; color: white;">
                This is an automated email, please do not reply directly to this message.
            </p>
        </div>
    </div>
</body>
</html>
