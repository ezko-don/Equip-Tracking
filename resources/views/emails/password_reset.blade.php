<!DOCTYPE html>
<html>
<head>
    <title>Your Password Has Been Reset</title>
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
            <h2>Your Password Has Been Reset</h2>
        </div>
        
        <div class="content">
            <p>Dear {{ $name }},</p>
            
            <p>Your password for the Strathmore Equipment Management System has been reset by an administrator. You can now log in with your new credentials.</p>
            
            <div class="credentials">
                <h3>Your New Login Credentials</h3>
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>New Password:</strong> {{ $password }}</p>
                <p><strong>Note:</strong> Please keep this information secure and consider changing your password after you log in.</p>
            </div>
            
            <p>To log in with your new password, click the button below:</p>
            
            <div style="text-align: center;">
                <a href="{{ $login_url }}" class="button">Login Now</a>
            </div>
            
            <p>If you did not request a password reset or need assistance, please contact the system administrator immediately.</p>
            
            <p>Best regards,<br>Strathmore Equipment Management Team</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Strathmore University. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html> 