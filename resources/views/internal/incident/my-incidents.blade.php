@extends('layouts.app')

@section('content')
    <style>
        body {
            background: #0f1419;
            color: #ffffff;
        }

        .incidents-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .incidents-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-report {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.95em;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-report:hover {
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(20, 30, 50, 0.6);
            padding: 24px;
            border-radius: 12px;
            border: 1px solid rgba(6, 182, 212, 0.15);
            backdrop-filter: blur(20px);

        }

        .stat-card.red {
            border-top-color: #ef4444;
        }

        .stat-card.orange {
            border-top-color: #f59e0b;
        }

        .stat-card.green {
            border-top-color: #10b981;
        }

        .stat-label {
            font-size: 0.85em;
            color: #6b7a90;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 2.5em;
            font-weight: 700;
            color: #06b6d4;
        }

        .incidents-container {
            background: rgba(20, 30, 50, 0.6);
            padding: 28px;
            border-radius: 12px;
            border: 1px solid rgba(6, 182, 212, 0.15);
            backdrop-filter: blur(20px);
        }

        .incident-card {
            padding: 20px;
            margin-bottom: 16px;
            border-left: 4px solid;
            background: rgba(6, 182, 212, 0.04);
            border-radius: 8px;
            transition: all 0.3s;
        }

        .incident-card:hover {
            background: rgba(6, 182, 212, 0.08);
        }

        .incident-card.resolved {
            border-left-color: #10b981;
        }

        .incident-card.in-progress {
            border-left-color: #f59e0b;
        }

        .incident-card.open {
            border-left-color: #ef4444;
        }

        .incident-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .incident-title {
            margin: 0 0 8px 0;
            font-size: 1.1em;
            font-weight: 700;
            color: #ffffff;
        }

        .incident-resource {
            margin: 0;
            color: #6b7a90;
            font-size: 0.9em;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.85em;
        }

        .status-badge.resolved {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
        }

        .status-badge.in-progress {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }

        .status-badge.open {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
        }

        .incident-description {
            margin: 12px 0;
            color: #a0aec0;
            line-height: 1.6;
        }

        .incident-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9em;
            color: #6b7a90;
        }

        .priority-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 0.85em;
        }

        .priority-critical {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
        }

        .priority-high {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }

        .priority-medium {
            background: rgba(59, 130, 246, 0.15);
            color: #93c5fd;
        }

        .priority-low {
            background: rgba(6, 182, 212, 0.15);
            color: #67e8f9;
        }

        .empty-state {
            padding: 60px 40px;
            text-align: center;
            color: #6b7a90;
        }

        .empty-state p {
            font-size: 1.1em;
            margin-bottom: 12px;
        }

        .empty-state a {
            color: #06b6d4;
            text-decoration: none;
            font-weight: 700;
        }

        .empty-state a:hover {
            text-decoration: underline;
        }

        .pagination {
            margin-top: 20px;
        }
    </style>

    <div style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
        <div class="incidents-header">
            <h1 class="incidents-title">Mes Incidents Signalés</h1>
            <a href="{{ route('internal.incidents.report') }}" class="btn-report">
                + Signaler un Incident
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total</div>
                <div class="stat-value">{{ $incidents->total() }}</div>
            </div>
            <div class="stat-card red">
                <div class="stat-label">Ouverts</div>
                <div class="stat-value">{{ $incidents->where('status', 'ouvert')->count() }}</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-label">En Cours</div>
                <div class="stat-value">{{ $incidents->where('status', 'en_cours')->count() }}</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Résolus</div>
                <div class="stat-value">{{ $incidents->where('status', 'resolu')->count() }}</div>
            </div>
        </div>

        <!-- Incidents List -->
        <div class="incidents-container">
            @forelse($incidents as $incident)
                <div
                    class="incident-card {{ $incident->status === 'resolu' ? 'resolved' : ($incident->status === 'en_cours' ? 'in-progress' : 'open') }}">
                    <div class="incident-header">
                        <div>
                            <h3 class="incident-title">{{ $incident->title }}</h3>
                            <p class="incident-resource">
                                <strong>Ressource:</strong> {{ $incident->resource->name ?? 'N/A' }}
                            </p>
                        </div>
                        <span
                            class="status-badge {{ $incident->status === 'resolu' ? 'resolved' : ($incident->status === 'en_cours' ? 'in-progress' : 'open') }}">
                            {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                        </span>
                    </div>

                    <p class="incident-description">{{ $incident->description }}</p>

                    <div class="incident-footer">
                        <div>
                            <span class="priority-badge priority-{{ $incident->priority ?? 'medium' }}">
                                Priorité: {{ ucfirst($incident->priority) }}
                            </span>
                        </div>
                        <small>{{ $incident->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <p>Aucun incident signalé</p>
                    <a href="{{ route('internal.incidents.report') }}">
                        Signaler un incident
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if ($incidents->hasPages())
        <div class="pagination" style="max-width: 1200px; margin: 20px auto;">
            {{ $incidents->links() }}
        </div>
    @endif
@endsection