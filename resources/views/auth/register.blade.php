<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg-body: #0a0e17;
            --bg-card: rgba(20, 30, 50, 0.6);
            --border-card: rgba(255, 255, 255, 0.08);
            --text-primary: #ffffff;
            --text-secondary: #a0aec0;
            --text-tertiary: #6b7a90;
            --accent: #06b6d4;
            --accent-light: #22d3ee;
            --accent-glow: rgba(6, 182, 212, 0.5);
            --gradient-btn: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%);
            --orb-color-1: #06b6d4;
            --orb-color-2: #0891b2;
            --error: #ef4444;
            --error-bg: rgba(239, 68, 68, 0.1);
        }

        html {
            height: 100%;
        }
        
        body {
            min-height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* Background orbs */
        .bg-orbs {
            position: fixed;
            inset: 0;
            overflow: hidden;
            z-index: 0;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            animation: float 20s ease-in-out infinite;
        }

        .orb-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--orb-color-1) 0%, transparent 70%);
            top: -150px;
            right: 20%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--orb-color-2) 0%, transparent 70%);
            bottom: -100px;
            right: -50px;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, var(--orb-color-1) 0%, transparent 70%);
            top: 50%;
            left: -100px;
            opacity: 0.3;
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* Main container */
        .register-container {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 1;
            padding: 20px 0;
        }

        /* Left side - Welcome */
        .welcome-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
        }

        .welcome-content {
            max-width: 600px;
        }

        .welcome-title {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.03em;
            margin-bottom: 40px;
            background: linear-gradient(135deg, #ffffff 0%, #a0aec0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .tagline-box {
            display: inline-flex;
            align-items: center;
            gap: 30px;
        }

        .tagline {
            padding: 14px 28px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 1.1rem;
            font-weight: 400;
            font-style: italic;
            color: var(--text-secondary);
            background: rgba(255, 255, 255, 0.02);
        }

        .dashed-line {
            width: 200px;
            height: 1px;
            background: repeating-linear-gradient(
                90deg,
                var(--text-tertiary) 0px,
                var(--text-tertiary) 8px,
                transparent 8px,
                transparent 16px
            );
        }

        /* Right side - Register form */
        .form-section {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            min-width: 480px;
            margin: auto 0;
        }

        .register-card {
            width: 100%;
            max-width: 420px;
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-card);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        .register-header {
            margin-bottom: 36px;
        }

        .register-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .register-subtitle {
            font-size: 0.95rem;
            color: var(--text-secondary);
        }

        /* Error messages */
        .error-box {
            background: var(--error-bg);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
        }

        .error-box ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .error-box li {
            color: var(--error);
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .error-box li + li {
            margin-top: 6px;
        }

        /* Success messages */
        .success-box {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
            color: #10b981;
            font-size: 0.875rem;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 16px;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            outline: none;
            transition: all 0.25s ease;
        }

        .form-input::placeholder {
            color: var(--text-tertiary);
        }

        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.15);
            background: rgba(255, 255, 255, 0.05);
        }

        /* Submit button */
        .btn-register {
            width: 100%;
            padding: 16px 24px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            color: #0a0e17;
            background: var(--gradient-btn);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 20px;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px -10px var(--accent-glow);
        }

        .btn-register:hover::before {
            opacity: 1;
        }

        .btn-register:active {
            transform: translateY(0);
        }

        /* Login link */
        .login-link {
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 20px;
            margin-bottom: 24px;
        }

        .login-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            margin-left: 4px;
        }

        .login-link a:hover {
            color: var(--accent-light);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 28px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .divider span {
            color: var(--text-tertiary);
            font-size: 0.85rem;
        }

        /* Social buttons */
        .social-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 32px;
        }

        .social-btn {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .social-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .social-btn svg {
            width: 22px;
            height: 22px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .register-container {
                flex-direction: column;
            }

            .welcome-section {
                padding: 40px 30px;
                align-items: center;
                text-align: center;
            }

            .welcome-title {
                font-size: 2.5rem;
                margin-bottom: 24px;
            }

            .tagline-box {
                flex-direction: column;
                gap: 20px;
            }

            .dashed-line {
                width: 100px;
                display: none;
            }

            .form-section {
                min-width: auto;
                padding: 20px;
            }

            .register-card {
                padding: 36px 28px;
            }
        }

        @media (max-width: 480px) {
            .welcome-section {
                padding: 30px 20px;
            }

            .welcome-title {
                font-size: 2rem;
            }

            .tagline {
                padding: 10px 18px;
                font-size: 0.95rem;
            }

            .form-section {
                padding: 15px;
            }

            .register-card {
                padding: 28px 22px;
                border-radius: 20px;
            }

            .register-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <!-- Background orbs -->
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="register-container">
        <!-- Left side - Welcome message -->
        <div class="welcome-section">
            <div class="welcome-content">
                <h1 class="welcome-title">Join Us Today !</h1>
                <div class="tagline-box">
                    <div class="tagline">Start managing resources</div>
                    <div class="dashed-line"></div>
                </div>
            </div>
        </div>

        <!-- Right side - Register form -->
        <div class="form-section">
            <div class="register-card">
                <div class="register-header">
                    <h2 class="register-title">Create Account</h2>
                    <p class="register-subtitle">Let's get you started</p>
                </div>

                @if ($errors->any())
                    <div class="error-box">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="success-box">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group">
                        <input type="text" id="name" name="name" class="form-input" placeholder="Full Name"
                            value="{{ old('name') }}" required autofocus>
                    </div>

                    <div class="form-group">
                        <input type="email" id="email" name="email" class="form-input" placeholder="Email"
                            value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Password"
                            required>
                    </div>

                    <div class="form-group">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input"
                            placeholder="Confirm Password" required>
                    </div>

                    <button type="submit" class="btn-register">Create Account</button>

                    <div class="divider">
                        <span>Or</span>
                    </div>

                    <div class="social-buttons">
                        <a href="{{ route('social.redirect', 'google') }}" class="social-btn" title="Continue with Google">
                            <svg viewBox="0 0 48 48">
                                <path fill="#FFC107"
                                    d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z" />
                                <path fill="#FF3D00"
                                    d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z" />
                                <path fill="#4CAF50"
                                    d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.221,0-9.652-3.343-11.303-8l-6.571,4.819C9.656,39.663,16.318,44,24,44z" />
                                <path fill="#1976D2"
                                    d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z" />
                            </svg>
                        </a>

                        <a href="{{ route('social.redirect', 'github') }}" class="social-btn" title="Continue with GitHub">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                            </svg>
                        </a>
                    </div>

                    <div class="login-link">
                        Already have an account?<a href="{{ route('login') }}">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Add any scripts here if needed
    </script>
</body>
</html>