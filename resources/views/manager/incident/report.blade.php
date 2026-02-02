@extends('layouts.app')

@section('content')
    <style>
        .incident-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .incident-container h1 {
            font-size: 2em;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 30px 0;
        }

        .incident-form {
            background: rgba(20, 30, 50, 0.6);
            padding: 32px;
            border-radius: 12px;
            border: 1px solid rgba(6, 182, 212, 0.2);
            backdrop-filter: blur(10px);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #ffffff;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 6px;
            color: #cbd5e1;
            font-size: 1em;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #06b6d4;
            box-shadow: 0 0 10px rgba(6, 182, 212, 0.3);
            background: rgba(15, 23, 42, 0.95);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #64748b;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            flex: 1;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1em;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .btn-submit {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        .btn-cancel {
            background: rgba(148, 163, 184, 0.1);
            color: #cbd5e1;
            border: 1px solid rgba(148, 163, 184, 0.2);
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: rgba(148, 163, 184, 0.2);
            color: #ffffff;
            border-color: rgba(148, 163, 184, 0.3);
        }

        .alert {
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 24px;
            border-left: 4px solid;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
            color: #fca5a5;
        }

        .alert-error ul {
            margin: 0;
            padding-left: 20px;
        }

        .alert-error li {
            margin-bottom: 6px;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border-color: #22c55e;
            color: #86efac;
        }

        .info-box {
            background: rgba(6, 182, 212, 0.05);
            border-left: 4px solid #06b6d4;
            padding: 20px;
            border-radius: 6px;
            margin-top: 30px;
        }

        .info-box h3 {
            margin: 0 0 12px 0;
            color: #06b6d4;
            font-weight: 600;
        }

        .info-box ul {
            margin: 0;
            padding-left: 20px;
            color: #cbd5e1;
            line-height: 1.6;
        }

        .info-box li {
            margin-bottom: 6px;
        }
    </style>

    <div class="incident-container">
        <h1><i class="fas fa-exclamation-circle"></i> Signaler un Incident</h1>

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

        <div class="incident-form">
            <form method="POST" action="{{ route('manager.incidents.store') }}">
                @csrf

                <div class="form-group">
                    <label for="title">Titre de l'Incident *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                        placeholder="Ex: Serveur ne répond pas" required>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea name="description" id="description" placeholder="Décrivez le problème en détail..."
                        style="resize: vertical; min-height: 120px;" required>{{ old('description') }}</textarea>
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
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}><i class="fas fa-circle"
                                style="color: #22c55e;"></i> Basse - Peut attendre</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}><i class="fas fa-circle"
                                style="color: #eab308;"></i> Moyenne - Normal</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}><i class="fas fa-circle"
                                style="color: #ef4444;"></i> Élevée - Urgent</option>
                        <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>⛔ Critique - Immédiat
                        </option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-submit">
                        Signaler l'Incident
                    </button>
                    <a href="{{ route('manager.dashboard') }}" class="btn btn-cancel">
                        Annuler
                    </a>
                </div>
            </form>
        </div>

        <!-- Information Box -->
        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> Informations pour les Gestionnaires</h3>
            <ul>
                <li>Vous pouvez signaler des incidents pour toutes les ressources du système</li>
                <li>Décrivez précisément le problème technique rencontré pour une résolution rapide</li>
                <li>L'équipe administrative sera immédiatement notifiée de votre signalement</li>
                <li>Les incidents critiques reçoivent une attention prioritaire</li>
                <li>Vous recevrez des mises à jour sur l'état de votre signalement</li>
            </ul>
        </div>
    </div>
@endsection