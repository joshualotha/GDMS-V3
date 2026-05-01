<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
        }
        
        .split-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        
        /* Left Side - Branding */
        .brand-side {
            flex: 1;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }
        
        .brand-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M50 50V30h-10v20H30v10h20v20h10V50h20V40h-10zM0 0h20v20H0V0zM60 60h20v20H60V60z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .brand-content {
            text-align: center;
            position: relative;
            z-index: 1;
            max-width: 400px;
        }
        
        .brand-logo {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .brand-logo svg {
            width: 56px;
            height: 56px;
            color: #ffffff;
        }
        
        .brand-title {
            color: #ffffff;
            font-size: 42px;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 16px;
        }
        
        .brand-tagline {
            color: rgba(255, 255, 255, 0.7);
            font-size: 18px;
            font-weight: 400;
            line-height: 1.6;
            margin-bottom: 48px;
        }
        
        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
            text-align: left;
        }
        
        .brand-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }
        
        .brand-feature-icon {
            width: 32px;
            height: 32px;
            background: rgba(59, 130, 246, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .brand-feature-icon svg {
            width: 16px;
            height: 16px;
            color: #60a5fa;
        }
        
        /* Right Side - Login Form */
        .form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
            background: #ffffff;
        }
        
        .form-container {
            width: 100%;
            max-width: 400px;
        }
        
        .form-header {
            margin-bottom: 40px;
        }
        
        .form-header h2 {
            color: #111827;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .form-header p {
            color: #6b7280;
            font-size: 15px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .form-input {
            width: 100%;
            height: 52px;
            padding: 0 16px;
            font-size: 15px;
            color: #1f2937;
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            outline: none;
            transition: all 0.2s ease;
        }
        
        .form-input:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        
        .form-input::placeholder {
            color: #9ca3af;
        }
        
        .btn-submit {
            width: 100%;
            height: 52px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 8px;
        }
        
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(30, 64, 175, 0.4);
        }
        
        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 24px;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
            color: #6b7280;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .brand-side {
                display: none;
            }
            
            .form-side {
                flex: none;
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .form-side {
                padding: 24px;
            }
            
            .form-container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="split-container">
        <!-- Left Side - Branding -->
        <div class="brand-side">
            <div class="brand-content">
                <div class="brand-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M.75 15.75c0 3.792 2.246 7.17 6 9.333 3.754-2.163 6-3.541 6-9.333-.75-1.125-.75-2.25 0-3.375 2.25-1.125 3.75-2.625 3.75-4.125 0-.933-.75-1.5-1.5-1.5H2.25c-.75 0-1.5.567-1.5 1.5 0 1.5 1.5 3 3.75 4.125 0 1.5.75 2.25.75 3.375z" />
                    </svg>
                </div>
                <h1 class="brand-title">GDMS</h1>
                <p class="brand-tagline">
                    Streamline your gas distribution operations with our comprehensive management system
                </p>
                
                <div class="brand-features">
                    <div class="brand-feature">
                        <div class="brand-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </div>
                        <span>Inventory Management</span>
                    </div>
                    <div class="brand-feature">
                        <div class="brand-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span>Financial Tracking</span>
                    </div>
                    <div class="brand-feature">
                        <div class="brand-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                        </div>
                        <span>Sales & Distribution</span>
                    </div>
                    <div class="brand-feature">
                        <div class="brand-feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 003 12V5.25" />
                            </svg>
                        </div>
                        <span>Asset Management</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="form-side">
            <div class="form-container">
                <div class="form-header">
                    <h2>Welcome back</h2>
                    <p>Enter your credentials to access your account</p>
                </div>
                
                @if($errors->any())
                    <div class="error-message">
                        {{ $errors->first() }}
                    </div>
                @endif
                
                @yield('content')
                
                <div class="form-footer">
                    <p>&copy; {{ date('Y') }} GDMS. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>