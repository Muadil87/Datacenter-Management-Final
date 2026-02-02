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

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-primary {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            box-shadow: 0 0 12px rgba(6, 182, 212, 0.4);
            transform: translateY(-2px);
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

        .stat-label {
            font-size: 0.85em;
            color: var(--text-secondary);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 2em;
            font-weight: 700;
            color: var(--accent);
        }

        .calendar-wrapper {
            background: var(--bg-card);
            padding: 28px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(20px);
        }

        .calendar-container {
            height: auto;
            overflow-y: auto;
        }

        .maintenance-item {
            padding: 16px;
            margin-bottom: 12px;
            border-left: 4px solid;
            background: var(--bg-dark);
            border-radius: 6px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .maintenance-item:hover {
            border-color: var(--accent);
            box-shadow: 0 0 12px rgba(6, 182, 212, 0.2);
        }

        .maintenance-item.completed {
            border-left-color: #10b981;
        }

        .maintenance-item.in-progress {
            border-left-color: #f59e0b;
        }

        .maintenance-item.scheduled {
            border-left-color: #ef4444;
        }

        .maintenance-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .maintenance-info {
            flex: 1;
        }

        .maintenance-title {
            margin: 0 0 8px 0;
            font-weight: 600;
            color: var(--text-primary);
        }

        .maintenance-detail {
            margin: 0 0 6px 0;
            font-size: 0.9em;
            color: var(--text-secondary);
        }

        .maintenance-detail strong {
            color: var(--text-primary);
        }

        .maintenance-actions {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            box-shadow: 0 0 12px rgba(6, 182, 212, 0.4);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85em;
        }

        .status-completed {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-in_progress {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-scheduled {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-cancel:hover {
            background: var(--text-secondary);
        }
    </style>

    <div class="page-container">
        <div class="header-section">
            <h1 class="page-title"><i class="fas fa-calendar"></i> Calendrier de Maintenance</h1>
            <a href="{{ route('admin.maintenance.create') }}" class="btn-primary">
                + Programmer Maintenance
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-label">Total</div>
                <div class="stat-value">{{ count($maintenances) }}</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-label">Programmées</div>
                <div class="stat-value">{{ $maintenances->where('status', 'scheduled')->count() }}</div>
            </div>
            <div class="stat-card red">
                <div class="stat-label">En cours</div>
                <div class="stat-value">{{ $maintenances->where('status', 'in_progress')->count() }}</div>
            </div>
            <div class="stat-card green">
                <div class="stat-label">Complétées</div>
                <div class="stat-value">{{ $maintenances->where('status', 'completed')->count() }}</div>
            </div>
        </div>

        <!-- Calendar Container -->
        <div class="calendar-wrapper">
            <div class="calendar-container">
                @foreach($maintenances as $maintenance)
                    <div class="maintenance-item {{ strtolower($maintenance->status) }}">
                        <div class="maintenance-header">
                            <div class="maintenance-info">
                                <h3 class="maintenance-title">{{ $maintenance->title }}</h3>
                                <p class="maintenance-detail">
                                    <strong>Ressource:</strong> {{ $maintenance->resource->name }}
                                </p>
                                <p class="maintenance-detail">
                                    <strong>Raison:</strong> {{ $maintenance->reason }}
                                </p>
                                <p class="maintenance-detail">
                                    <strong>Période:</strong>
                                    {{ \Carbon\Carbon::parse($maintenance->start_at)->format('d/m/Y H:i') }} -
                                    {{ \Carbon\Carbon::parse($maintenance->end_at)->format('d/m/Y H:i') }}
                                </p>
                                <p class="maintenance-detail">
                                    <span class="status-badge status-{{ strtolower($maintenance->status) }}">
                                        {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                                    </span>
                                </p>
                            </div>
                            <div class="maintenance-actions">
                                @if($maintenance->status !== 'completed' && $maintenance->status !== 'cancelled')
                                    <form method="POST" action="{{ route('admin.maintenance.status', $maintenance->id) }}"
                                        style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status"
                                            value="{{ $maintenance->status === 'scheduled' ? 'in_progress' : 'completed' }}">
                                        <button type="submit" class="btn-action">
                                            {{ $maintenance->status === 'scheduled' ? 'Démarrer' : 'Terminer' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection