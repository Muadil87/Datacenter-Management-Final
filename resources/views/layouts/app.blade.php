<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Data Center</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    {{-- Unified Sidebar Layout for All Users (Restricted to Admin) --}}
    @auth
        @if(Auth::user()->role === 'admin')
            @include('stats.sidebar')
        @endif
    @endauth

    <div class="page-wrapper" style="background-color: #0f1419 !important;">
        @include('partials.navbar')

        <main class="app-main"
            style="flex: 1; padding: 40px; overflow-y: auto; max-width: 100%; margin: 0; background-color: #0f1419 !important;">
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script src="{{ asset('js/main.js') }}"></script>
</body>

</html>