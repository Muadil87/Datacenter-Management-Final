@extends('layout')

@section('content')
    <div style="max-width: 600px; margin: 60px auto; text-align: center;">

        <div
            style="background: rgba(30, 41, 59, 0.6); padding: 50px; border-radius: 12px; border: 1px solid rgba(148, 163, 184, 0.2);">

            <div style="font-size: 4rem; margin-bottom: 20px;">⏳</div>

            <h1 style="color: #ffffff; margin-bottom: 10px;">Account Pending Verification</h1>

            <p style="color: #cbd5e1; font-size: 1.1em; margin-bottom: 30px; line-height: 1.6;">
                Thank you for signing up! Your account has been created successfully.
                <br><br>
                An administrator needs to verify your account before you can access the system.
                <br><br>
                You will receive an email once your account has been approved.
            </p>

            @if(session('email'))
                <div
                    style="background: rgba(6, 182, 212, 0.1); padding: 15px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #06b6d4;">
                    <p style="color: #67e8f9; margin: 0;">
                        <strong>Email:</strong> {{ session('email') }}
                    </p>
                </div>
            @endif

            <div style="display: flex; gap: 12px; justify-content: center; margin-top: 30px;">
                <a href="{{ route('home') }}"
                    style="display: inline-block; background: #334155; color: #f1f5f9; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.2s;">
                    ← Back to Home
                </a>
                <a href="{{ route('login') }}"
                    style="display: inline-block; background: #06b6d4; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.2s;">
                    Login with another account
                </a>
            </div>

            <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid rgba(148, 163, 184, 0.2);">
                <p style="color: #94a3b8; font-size: 0.9em; margin: 0;">
                    <i class="fas fa-info-circle"></i>
                    This typically takes 24 hours. If you don't receive an email, please contact support.
                </p>
            </div>

        </div>

    </div>
@endsection