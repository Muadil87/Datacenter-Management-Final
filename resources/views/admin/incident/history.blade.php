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
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0 0 30px 0;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--bg-card);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(20px);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: var(--accent);
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.2);
        }

        .stat-value {
            font-size: 2em;
            font-weight: 700;
            color: var(--accent);
        }

        .table-wrapper {
            background: var(--bg-card);
            padding: 28px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(20px);
            overflow-x: auto;
        }

        .table-title {
            margin: 0 0 20px 0;
            font-size: 1.3em;
            color: var(--text-primary);
            font-weight: 600;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            border-bottom: 2px solid var(--border-color);
        }

        .data-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: var(--accent);
            font-size: 0.9em;
            text-transform: uppercase;
        }

        .data-table td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .data-table tbody tr:hover {
            background-color: rgba(6, 182, 212, 0.05);
        }

        .incident-id {
            font-weight: 600;
            color: var(--accent);
        }

        .incident-user {
            color: var(--text-secondary);
        }

        .incident-resource {
            color: var(--text-secondary);
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85em;
        }

        .status-resolu {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-en_cours {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-ouvert {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .incident-date {
            color: var(--text-tertiary);
            font-size: 0.9em;
        }

        .empty-state {
            padding: 20px;
            text-align: center;
            color: var(--text-tertiary);
        }

        .pagination-wrapper {
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            border-color: var(--accent);
            background: rgba(6, 182, 212, 0.1);
        }

        .pagination .active span {
            background: var(--accent);
            color: var(--bg-dark);
            border-color: var(--accent);
        }
    </style>

    <div class="page-container">
        <h1 class="page-title"><i class="fas fa-chart-bar"></i> Historique des Incidents</h1>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-label">Total</div>
                <div class="stat-value">{{ $incidentStats['total'] }}</div>
            </div>
            <div class="stat-card red">
                <div class="stat-label">Ouverts</div>
                <div class="stat-value">{{ $incidentStats['ouvert'] }}</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-label">En Cours</div>
                <div class="stat-value">{{ $incidentStats['en_cours'] }}</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Résolus</div>
                <div class="stat-value">{{ $incidentStats['resolu'] }}</div>
            </div>
        </div>

        <!-- Incidents Table -->
        <div class="table-wrapper">
            <h2 class="table-title">Détails des Incidents</h2>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Utilisateur</th>
                        <th>Ressource</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incidents as $incident)
                        <tr>
                            <td class="incident-id">#{{ $incident->id }}</td>
                            <td>{{ $incident->title }}</td>
                            <td class="incident-user">{{ $incident->user->name ?? 'N/A' }}</td>
                            <td class="incident-resource">{{ $incident->resource->name ?? 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $incident->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                                </span>
                            </td>
                            <td class="incident-date">{{ $incident->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">Aucun incident enregistré</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $incidents->links() }}
            </div>
        </div>
    </div>
@endsection