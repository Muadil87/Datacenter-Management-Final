<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DataCenter </title>

    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🖥️</text></svg>">
    <style>
        html {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            background: #0f1419;
            color: #ffffff;
            display: flex;
            flex-direction: row;
            min-height: 100vh;
            overflow-x: hidden;
        }

        * {
            box-sizing: border-box;
        }
    </style>
</head>

<body>

    @auth
        @if(Auth::user()->role === 'admin')
            @include('stats.sidebar')
        @endif
    @endauth

    <div class="page-wrapper">
        @include('partials.navbar')

        <main style="flex: 1; overflow-y: auto; overflow-x: hidden; display: flex; flex-direction: column; width: 100%; min-height: 100vh;">
            <div class="container" style="flex: 1; display: flex; flex-direction: column;">
                @yield('content')
            </div>

            <footer
                style="background: rgba(15, 20, 25, 0.8); color: #a0aec0; padding: 20px 0; margin-top: auto; text-align: center; flex-shrink: 0; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                <div class="container">
                    <p>&copy; {{ date('Y') }} DataCenter Management System. {{ __('All rights reserved.') }}</p>
                    <p style="font-size: 0.8em; color: #6b7a90;">
                        <a href="{{ route('rules') }}"
                            style="color: #06b6d4; text-decoration: none; transition: color 0.2s;">{{ __('Usage Charter') }}</a>
                    </p>
                </div>
            </footer>
        </main>
    </div>

    <script src="{{ asset('js/main.js') }}"></script>
</body>

</html>