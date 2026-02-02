@extends('layout')

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
            background: var(--bg-dark);
            color: var(--text-primary);
        }

        .incidents-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
            background: var(--bg-dark);
            min-height: 100vh;
        }

        .incidents-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 30px 0;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .alert-content {
            flex: 1;
        }

        .alert-content strong {
            display: block;
            margin-bottom: 5px;
            color: #10b981;
        }

        .alert-content p {
            margin: 0;
            font-size: 0.95em;
        }

        .alert-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-tertiary);
            cursor: pointer;
            padding: 0;
            margin-left: 15px;
            transition: all 0.3s;
        }

        .alert-close:hover {
            color: var(--accent);
        }

        .table-wrapper {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            overflow-x: auto;
            border: 1px solid var(--border-color);
        }

        .table-title {
            margin: 0 0 20px 0;
            font-size: 1.3em;
            color: var(--text-primary);
            font-weight: 700;
        }

        .incidents-table {
            width: 100%;
            border-collapse: collapse;
        }

        .incidents-table thead {
            background: rgba(6, 182, 212, 0.08);
            border-bottom: 2px solid rgba(6, 182, 212, 0.2);
        }

        .incidents-table th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 700;
            color: var(--accent);
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .incidents-table tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s;
        }

        .incidents-table tbody tr:hover {
            background: rgba(6, 182, 212, 0.06);
        }

        .incidents-table td {
            padding: 14px 16px;
            color: var(--text-secondary);
            font-size: 0.95em;
        }

        .user-cell {
            font-weight: 600;
            color: var(--text-primary);
        }

        .user-email {
            font-size: 0.85em;
            color: var(--text-tertiary);
            margin-top: 4px;
        }

        .title-cell {
            font-weight: 500;
            color: var(--text-primary);
        }

        .date-cell {
            color: var(--text-tertiary);
            font-size: 0.9em;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.85em;
            border: 1px solid;
        }

        .status-ouvert {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .status-en_traitement {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border-color: rgba(245, 158, 11, 0.3);
        }

        .status-resolu {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border-color: rgba(16, 185, 129, 0.3);
        }

        .action-link {
            color: var(--accent);
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
            display: inline-block;
        }

        .action-link:hover {
            color: var(--accent-light);
            text-shadow: 0 0 10px rgba(6, 182, 212, 0.4);
        }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-tertiary);
        }

        .empty-state p {
            font-size: 1.05em;
            margin: 0;
        }
    </style>

    <div class="incidents-wrapper">
        <h1 class="incidents-title"><i class="fas fa-list"></i> Incidents Signalés</h1>

        @if(session('success'))
            <div class="alert alert-success">
                <div class="alert-content">
                    <strong>Succès</strong>
                    <p>{{ session('success') }}</p>
                </div>
                <button class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        <!-- Incidents Table -->
        <div class="table-wrapper">
            <h2 class="table-title">Gestion des Incidents des Utilisateurs Internes</h2>

            @if($incidents->count() > 0)
                <table class="incidents-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Ressource</th>
                            <th>Titre</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incidents as $incident)
                            <tr>
                                <td>
                                    <div class="user-cell">{{ $incident->user->name }}</div>
                                    <div class="user-email">{{ $incident->user->email }}</div>
                                </td>
                                <td>{{ $incident->resource->name ?? 'Ressource supprimée' }}</td>
                                <td class="title-cell">{{ $incident->title }}</td>
                                <td class="date-cell">{{ $incident->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="status-badge status-{{ $incident->status }}">
                                        {{ $incident->status === 'resolu' ? 'Résolu' : ($incident->status === 'en_traitement' ? 'En cours' : 'Ouvert') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('manager.incidents.show', $incident->id) }}" class="action-link">
                                        Détails & Gérer
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <p>✨ Aucun incident signalé par les utilisateurs internes</p>
                </div>
            @endif
        </div>
    </div>
@endsection
