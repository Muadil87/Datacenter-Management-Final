@extends('layout')

@section('content')
    <div class="manager-container">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-wrench"></i> Planifier une Maintenance</h1>
                <p class="subtitle">Gérer la maintenance des ressources</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <div class="alert-content">
                    <strong>Succès</strong>
                    <p>{{ session('success') }}</p>
                </div>
                <button class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        <div class="maintenance-layout">
            <!-- Main Panel: Maintenance Form + Scheduled -->
            <div class="maintenance-form-panel">
                <h2><i class="fas fa-wrench"></i> Planifier une Maintenance</h2>

                <div
                    style="background: rgba(6, 182, 212, 0.1); border-left: 4px solid #06b6d4; padding: 1rem; margin-bottom: 1.5rem; border-radius: 6px; border: 1px solid rgba(6, 182, 212, 0.2);">
                    <p style="margin: 0; color: #06b6d4; font-size: 0.9rem;">
                        <strong><i class="fas fa-info-circle"></i> Fonctionnement Automatique:</strong><br>
                        • La ressource passera automatiquement en maintenance à la date de début<br>
                        • Elle reviendra automatiquement en inventaire à la date de fin
                    </p>
                </div>

                <form method="POST" action="{{ route('manager.maintenance.create') }}">
                    @csrf
                    <div class="form-group">
                        <label for="resource_id">Ressource</label>
                        <select name="resource_id" id="resource_id" class="form-control" required>
                            <option value="">Choisir une ressource...</option>
                            @foreach($resources as $resource)
                                <option value="{{ $resource->id }}">
                                    {{ $resource->name }} ({{ $resource->state }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="start_at">Date de Début de Maintenance</label>
                        <input type="datetime-local" name="start_at" id="start_at" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="end_at">Date de Fin de Maintenance</label>
                        <input type="datetime-local" name="end_at" id="end_at" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description de la Maintenance</label>
                        <textarea name="description" id="description" class="form-control" rows="5"
                            placeholder="Détails de la maintenance à effectuer..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-check"></i> Planifier la Maintenance
                    </button>
                </form>

                <!-- Maintenances Planifiées - Moved Here -->
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e0e0e0;">
                    <h2><i class="fas fa-calendar"></i> Maintenances Planifiées</h2>
                    @php
                        $plannedMaintenances = \App\Models\Maintenance::where('status', 'scheduled')
                            ->orWhere('status', 'in_progress')
                            ->with(['resource'])
                            ->orderBy('start_at', 'asc')
                            ->get();
                    @endphp

                    @if($plannedMaintenances->count() > 0)
                        <div class="maintenance-list">
                            @foreach($plannedMaintenances as $maintenance)
                                <div class="maintenance-card planned">
                                    <div class="maintenance-header">
                                        <h3>{{ $maintenance->resource->name }}</h3>
                                        <span class="badge badge-{{ $maintenance->status === 'in_progress' ? 'warning' : 'info' }}">
                                            {{ $maintenance->status === 'in_progress' ? 'En cours' : 'Planifiée' }}
                                        </span>
                                    </div>
                                    <div class="maintenance-details">
                                        <p><strong>Début:</strong>
                                            {{ \Carbon\Carbon::parse($maintenance->start_at)->format('d/m/Y H:i') }}</p>
                                        <p><strong>Fin:</strong>
                                            {{ \Carbon\Carbon::parse($maintenance->end_at)->format('d/m/Y H:i') }}</p>
                                        @if($maintenance->reason)
                                            <p><strong>Raison:</strong> {{ $maintenance->reason }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <p>✨ Aucune maintenance planifiée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .manager-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 2rem;
            background: #0f1419;
            min-height: calc(100vh - 200px);
        }

        .page-header {
            background: rgba(20, 30, 50, 0.4);
            backdrop-filter: blur(20px);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(6, 182, 212, 0.1);
            border: 1px solid rgba(6, 182, 212, 0.2);
        }

        .page-header h1 {
            margin: 0 0 0.5rem 0;
            font-size: 1.8rem;
            color: #ffffff;
        }

        .page-header .subtitle {
            margin: 0;
            opacity: 0.9;
            font-size: 0.95rem;
            color: #a0aec0;
        }

        .maintenance-layout {
            display: block;
            margin-bottom: 2rem;
        }

        .maintenance-form-panel {
            background: rgba(20, 30, 50, 0.4);
            backdrop-filter: blur(20px);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            max-width: 900px;
            margin: 0 auto;
            border: 1px solid rgba(6, 182, 212, 0.1);
        }

        .maintenance-form-panel h2 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            color: #ffffff;
            font-size: 1.3rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #ffffff;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 6px;
            font-size: 1rem;
            font-family: inherit;
            background: rgba(15, 20, 25, 0.5);
            color: #ffffff;
        }

        .form-control:focus {
            outline: none;
            border-color: #06b6d4;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.2);
        }

        .form-control::placeholder {
            color: #6b7a90;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #06b6d4;
            color: #0f1419;
        }

        .btn-primary:hover {
            background: #22d3ee;
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.4);
            transform: translateY(-2px);
        }

        .maintenance-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .maintenance-card {
            background: rgba(15, 20, 25, 0.4);
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #06b6d4;
            border: 1px solid rgba(6, 182, 212, 0.15);
        }

        .maintenance-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .maintenance-header h3 {
            margin: 0;
            font-size: 1.1rem;
            color: #ffffff;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-warning {
            background: rgba(249, 115, 22, 0.2);
            color: #f97316;
            border: 1px solid rgba(249, 115, 22, 0.3);
        }

        .badge-info {
            background: rgba(6, 182, 212, 0.2);
            color: #06b6d4;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }

        .maintenance-card.planned {
            border-left-color: #06b6d4;
            background: rgba(6, 182, 212, 0.08);
        }

        .maintenance-details p {
            margin: 0.5rem 0;
            font-size: 0.9rem;
            color: #a0aec0;
        }

        .maintenance-details strong {
            color: #06b6d4;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6b7a90;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
        }

        .alert-content {
            flex: 1;
        }

        .alert-content strong {
            color: #10b981;
        }

        .alert-content p {
            color: #a0aec0;
            margin: 0.25rem 0 0 0;
        }

        .alert-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #a0aec0;
            cursor: pointer;
            padding: 0;
            margin-left: 1rem;
        }

        .alert-close:hover {
            color: #06b6d4;
        }
    </style>
@endsection