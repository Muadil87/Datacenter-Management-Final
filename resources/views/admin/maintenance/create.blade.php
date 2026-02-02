@extends('layouts.app')

@section('content')
    <style>
        :root {
            --bg-dark: #0f1419;
            --bg-card: rgba(20, 30, 50, 0.6);
            --border-color: rgba(6, 182, 212, 0.15);
            --text-primary: #ffffff;
            --text-secondary: #a0aec0;
            --text-tertiary: #6b7a90;
            --accent: #06b6d4;
            --accent-light: #22d3ee;
        }

        body {
            background-color: var(--bg-dark) !important;
            color: var(--text-primary) !important;
        }

        html {
            background-color: var(--bg-dark) !important;
        }

        main {
            background-color: var(--bg-dark) !important;
        }

        .page-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-section {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .back-button {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.95em;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .back-button:hover {
            box-shadow: 0 0 12px rgba(6, 182, 212, 0.4);
            transform: translateY(-2px);
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            flex: 1;
        }

        .form-container {
            background: var(--bg-card);
            padding: 32px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(20px);
        }

        .form-title {
            margin: 0 0 24px 0;
            font-size: 1.5em;
            color: var(--accent);
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
        }

        input[type="text"],
        input[type="datetime-local"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            background-color: white;
            color: #333;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 1em;
            font-family: inherit;
            box-sizing: border-box;
        }

        input[type="text"]::placeholder,
        input[type="datetime-local"]::placeholder,
        textarea::placeholder {
            color: #999;
        }

        input[type="text"],
        input[type="datetime-local"],
        select {
            color: #333 !important;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            align-items: stretch;
        }

        .btn-submit,
        .btn-cancel {
            flex: 1;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1em;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
            text-decoration: none;
        }

        .btn-submit {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
        }

        .btn-submit:hover {
            box-shadow: 0 0 12px rgba(6, 182, 212, 0.4);
        }

        .btn-cancel {
            background: var(--text-tertiary);
            color: var(--text-primary);
        }

        .btn-cancel:hover {
            background: var(--text-secondary);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .error-message {
            color: #fca5a5;
            font-size: 0.9em;
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.6rem;
            }

            .form-container {
                padding: 20px;
            }

            .form-buttons {
                flex-direction: column;
            }
        }
    </style>

    <div class="page-container">
        <div class="header-section">
            <a href="{{ route('admin.maintenance.calendar') }}" class="back-button">← Retour</a>
            <h1 class="page-title"><i class="fas fa-wrench"></i> Programmer Maintenance</h1>
        </div>

        <div class="form-container">
            <h2 class="form-title">Nouvelle Maintenance</h2>

            @if ($errors->any())
                <div class="alert alert-error">
                    <span>❌</span>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.maintenance.schedule') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Ressource <span style="color: #fca5a5;">*</span></label>
                    <select name="resource_id" required>
                        <option value="">-- Sélectionner une ressource --</option>
                        @foreach($resources as $resource)
                            <option value="{{ $resource->id }}" {{ old('resource_id') == $resource->id ? 'selected' : '' }}>
                                {{ $resource->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('resource_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Titre <span style="color: #fca5a5;">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Ex: Maintenance préventive" required>
                    @error('title')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Raison <span style="color: #fca5a5;">*</span></label>
                    <textarea name="reason" placeholder="Décrivez la raison de la maintenance..." required>{{ old('reason') }}</textarea>
                    @error('reason')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Date & Heure de Début <span style="color: #fca5a5;">*</span></label>
                    <input type="datetime-local" name="start_at" value="{{ old('start_at') }}" required>
                    @error('start_at')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Date & Heure de Fin <span style="color: #fca5a5;">*</span></label>
                    <input type="datetime-local" name="end_at" value="{{ old('end_at') }}" required>
                    @error('end_at')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Programmer</button>
                    <a href="{{ route('admin.maintenance.calendar') }}" class="btn-cancel">Annuler</a>
                </div>
            </form>
        </div>
    </div>
@endsection
