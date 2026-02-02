@extends('layout')

@section('content')
    <div style="max-width: 600px; margin: 60px auto; text-align: center;">

        <div
            style="background: rgba(30, 41, 59, 0.6); padding: 50px; border-radius: 12px; border: 1px solid rgba(239, 68, 68, 0.3);">

            <div style="font-size: 4rem; margin-bottom: 20px;">🔒</div>

            <h1 style="color: #fca5a5; margin-bottom: 10px;">Account Deactivated</h1>

            <p style="color: #cbd5e1; font-size: 1.1em; margin-bottom: 30px; line-height: 1.6;">
                Your account has been deactivated by an administrator.
                <br><br>
                You no longer have access to the system.
                <br><br>
                If you believe this is a mistake, please contact the support team for assistance.
            </p>

            @if(session('email'))
                <div
                    style="background: rgba(239, 68, 68, 0.1); padding: 15px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #ef4444;">
                    <p style="color: #fca5a5; margin: 0;">
                        <strong>Account Email:</strong> {{ session('email') }}
                    </p>
                </div>
            @endif

            <div style="display: flex; gap: 12px; justify-content: center; margin-top: 30px;">
                <a href="{{ route('home') }}"
                    style="display: inline-block; background: #334155; color: #f1f5f9; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.2s;">
                    ← Back to Home
                </a>
                <a href="{{ route('login') }}"
                    style="display: inline-block; background: #ef4444; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.2s;">
                    Try another account
                </a>
            </div>

            <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid rgba(148, 163, 184, 0.2);">
                <p style="color: #94a3b8; font-size: 0.9em; margin: 0;">
                    <i class="fas fa-info-circle"></i>
                    For more information, please contact the support team.
                </p>
            </div>

        </div>

    </div>
@endsection