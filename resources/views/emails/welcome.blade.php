<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Strathmore Equipment Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .header {
            background-color: #32568E;
            color: white;
            padding: 10px 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .credentials {
            background-color: #e9ecef;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #32568E;
        }
        .button {
            display: inline-block;
            background-color: #32568E;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/strathmore-logo.png') }}" alt="Strathmore University" style="max-width: 200px;">
        </div>
        
        <div class="header">
            <h2>Welcome to Strathmore Equipment Management System</h2>
        </div>
        
        <div class="content">
            <p>Dear {{ $name }},</p>
            
            <p>Your account has been created in the Strathmore Equipment Management System. You can now log in and book equipment, manage your bookings, and more.</p>
            
            <div class="credentials">
                <h3>Your Login Credentials</h3>
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Password:</strong> {{ $password }}</p>
                <p><strong>Important:</strong> For security reasons, please change your password immediately after your first login.</p>
            </div>
            
            <p>To get started, click the button below to log in:</p>
            
            <div style="text-align: center;">
                <a href="{{ $login_url }}" class="button">Login Now</a>
            </div>
            
            <p>If you have any questions or need assistance, please contact the system administrator.</p>
            
            <p>Best regards,<br>Strathmore Equipment Management Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Strathmore University. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html> 