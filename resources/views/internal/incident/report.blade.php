@extends('layouts.app')

@section('content')
    <div class="incident-report-container">
        <h1 class="incident-title"><i class="fas fa-exclamation-circle"></i> Signaler un Incident</h1>

        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="form-card">
            <form method="POST" action="{{ route('internal.incidents.store') }}">
                @csrf

                <div class="form-group">
                    <label for="title">Titre de l'Incident *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                        placeholder="Ex: Serveur ne répond pas" required>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea name="description" id="description" placeholder="Décrivez le problème en détail..."
                        required>{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="resource_id">Ressource Affectée *</label>
                    <select name="resource_id" id="resource_id" required>
                        <option value="">-- Sélectionner une ressource --</option>
                        @foreach($resources as $resource)
                            <option value="{{ $resource->id }}" {{ (old('resource_id') ?? $preSelectedResource) == $resource->id ? 'selected' : '' }}>
                                {{ $resource->name }} ({{ $resource->state }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority">Niveau de Priorité *</label>
                    <select name="priority" id="priority" required>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                            <i class="fas fa-circle" style="color: #22c55e;"></i> Basse - Peut attendre
                        </option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>
                            <i class="fas fa-circle" style="color: #eab308;"></i> Moyenne - Normal
                        </option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                            <i class="fas fa-circle" style="color: #ef4444;"></i> Élevée - Urgent
                        </option>
                        <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>
                            ⛔ Critique - Immédiat
                        </option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-submit">
                        Signaler l'Incident
                    </button>
                    <a href="{{ route('internal.dashboard') }}" class="btn btn-cancel">
                        Annuler
                    </a>
                </div>
            </form>
        </div>

        <!-- Information Box -->
        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> Informations Importantes</h3>
            <ul>
                <li>Votre signalement sera traité rapidement par l'équipe administrative</li>
                <li>Décrivez le problème de manière claire et détaillée pour accélérer la résolution</li>
                <li>Vous recevrez une notification quand votre incident sera mis à jour</li>
                <li>Les incidents critiques sont traités en priorité</li>
            </ul>
        </div>
    </div>

    <style>
        .incident-report-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .incident-title {
            font-size: 2rem;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 30px 0;
        }

        .alert {
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 24px;
            font-size: 0.95rem;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .alert li {
            margin-bottom: 5px;
        }

        .alert.alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            color: #fca5a5;
        }

        .alert.alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-left: 4px solid #10b981;
            color: #a7f3d0;
        }

        .form-card {
            background: rgba(20, 30, 50, 0.6);
            padding: 32px;
            border-radius: 8px;
            border: 1px solid rgba(6, 182, 212, 0.2);
            backdrop-filter: blur(10px);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #cbd5e1;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 6px;
            font-size: 0.95rem;
            background: rgba(15, 23, 42, 0.8);
            color: #ffffff;
            font-family: inherit;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #06b6d4;
            background: rgba(15, 23, 42, 0.95);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-group select option {
            background: #0f1419;
            color: #ffffff;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.95rem;
            border: none;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 48px;
            box-sizing: border-box;
            line-height: 1;
        }

        .btn-submit {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 25px rgba(239, 68, 68, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-cancel {
            margin-top: 14px;
            background: rgba(148, 163, 184, 0.1);
            color: #94a3b8;
            border: none;
        }

        .btn-cancel:hover {
            background: rgba(148, 163, 184, 0.2);
            color: #cbd5e1;
        }

        .info-box {
            background: rgba(6, 182, 212, 0.1);
            border-left: 4px solid #06b6d4;
            padding: 16px;
            border-radius: 6px;
        }

        .info-box h3 {
            margin: 0 0 12px 0;
            color: #06b6d4;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .info-box ul {
            margin: 0;
            padding-left: 20px;
            color: #cbd5e1;
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .info-box li {
            margin-bottom: 8px;
        }
    </style>
@endsection