@extends('layout')

@section('content')
    <div class="manager-container">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-chart-bar"></i> Dashboard Gestionnaire</h1>
                <p class="subtitle">Vue d'ensemble et gestion des réservations de ressources</p>
            </div>
        </div>

        <!-- Quick Actions Buttons -->
        <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
            <a href="{{ route('manager.maintenance.list') }}"
                style="flex: 1; min-width: 200px; display: flex; align-items: center; justify-content: center; padding: 1rem; background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; transition: transform 0.2s; box-shadow: 0 0 20px rgba(6, 182, 212, 0.3);">
                <i class="fas fa-calendar"></i> Calendrier Maintenance
            </a>
            <a href="{{ route('manager.incidents.list') }}"
                style="flex: 1; min-width: 200px; display: flex; align-items: center; justify-content: center; padding: 1rem; background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; transition: transform 0.2s; box-shadow: 0 0 20px rgba(6, 182, 212, 0.3);">
                <i class="fas fa-list"></i> Incidents
            </a>
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

        <!-- Performance Metrics Section -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-value">{{ $performanceMetrics['totalRequests'] }}</div>
                <div class="metric-label">Demandes Totales</div>
                <div class="metric-bar">
                    <div class="metric-fill" style="width: 100%;"></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-value">{{ $performanceMetrics['approved'] }}</div>
                <div class="metric-label">Approuvées</div>
                <div class="metric-bar approved">
                    <div class="metric-fill" style="width: {{ min($performanceMetrics['approved'] * 10, 100) }}%;"></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-value">{{ $performanceMetrics['pending'] }}</div>
                <div class="metric-label">En Attente</div>
                <div class="metric-bar pending">
                    <div class="metric-fill" style="width: {{ min($performanceMetrics['pending'] * 10, 100) }}%;"></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-value">{{ $performanceMetrics['approvalRate'] }}%</div>
                <div class="metric-label">Taux d'Approbation</div>
                <div class="metric-bar success">
                    <div class="metric-fill" style="width: {{ $performanceMetrics['approvalRate'] }}%;"></div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-container">
            <!-- Reservation Timeline Chart -->
            <div class="chart-section">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-line"></i> Tendance des Réservations (30 derniers jours)</h3>
                    <p class="chart-subtitle">Nombre de nouvelles demandes par jour</p>
                </div>
                <div class="chart-wrapper">
                    <canvas id="reservationTimelineChart"></canvas>
                </div>
            </div>

            <!-- Resource Occupancy Heatmap -->
            <div class="chart-section full-width">
                <div class="chart-header">
                    <h3><i class="fas fa-temperature-high"></i> Taux d'Occupation des Ressources</h3>
                    <p class="chart-subtitle">Utilisation actuelle par ressource gérée</p>
                </div>
                <div class="occupancy-grid">
                    @if(count($occupancyData) > 0)
                        @foreach($occupancyData as $occupancy)
                            <div class="occupancy-item">
                                <div class="occupancy-header">
                                    <span class="occupancy-name">{{ $occupancy['name'] }}</span>
                                    <span class="occupancy-value">{{ $occupancy['percentage'] }}%</span>
                                </div>
                                <div class="occupancy-bar">
                                    <div class="occupancy-fill"
                                        style="width: {{ $occupancy['percentage'] }}%; background: {{ $occupancy['percentage'] > 70 ? '#e74c3c' : ($occupancy['percentage'] > 40 ? '#f39c12' : '#27ae60') }};">
                                    </div>
                                </div>
                                <div class="occupancy-details">
                                    <span>{{ $occupancy['occupied'] }} / {{ $occupancy['total'] }} réservations</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p style="grid-column: 1/-1; text-align: center; color: #7f8c8d; padding: 20px;">
                            Aucune ressource gérée. Les ressources qui vous sont assignées apparaîtront ici.
                        </p>
                    @endif
                </div>
            </div>
        </div>
        <div class="section">
            <div class="section-header">
                <div>
                    <h2>Demandes en Attente</h2>
                    <p class="section-description">Réservations en cours de validation</p>
                </div>
                <div class="section-badge">{{ count($pendingReservations) }}</div>
            </div>

            @if(count($pendingReservations) > 0)
                <div class="reservations-list">
                    @foreach($pendingReservations as $reservation)
                        <div class="reservation-item pending">
                            <div class="item-header">
                                <div class="item-main">
                                    <div class="item-title">{{ $reservation->user->name }}</div>
                                    <div class="item-subtitle">{{ $reservation->user->email }}</div>
                                </div>
                                <div class="item-status pending-status">En attente</div>
                            </div>

                            <div class="item-body">
                                <div class="info-grid">
                                    <div class="info-block">
                                        <span class="info-label">Ressource</span>
                                        <span class="info-value">{{ $reservation->resource->name }}</span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Catégorie</span>
                                        <span class="info-value">{{ $reservation->resource->category->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Date de début</span>
                                        <span
                                            class="info-value">{{ \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y') }}
                                            à {{ \Carbon\Carbon::parse($reservation->start_date)->format('H:i') }}</span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Date de fin</span>
                                        <span
                                            class="info-value">{{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') }}
                                            à {{ \Carbon\Carbon::parse($reservation->end_date)->format('H:i') }}</span>
                                    </div>
                                    <div class="info-block">
                                        <span class="info-label">Durée</span>
                                        <span
                                            class="info-value">{{ (int) \Carbon\Carbon::parse($reservation->start_date)->diffInDays(\Carbon\Carbon::parse($reservation->end_date)) + 1 }}
                                            jour(s)</span>
                                    </div>
                                </div>

                                <div class="justification-box">
                                    <span class="justification-label">Justification</span>
                                    <p class="justification-text">{{ $reservation->justification }}</p>
                                </div>
                            </div>

                            <div class="item-footer">
                                <form action="{{ route('reservations.handle', $reservation->id) }}" method="POST"
                                    class="action-form">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-primary">Approuver</button>
                                </form>

                                <form action="{{ route('reservations.handle', $reservation->id) }}" method="POST"
                                    class="action-form">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="refuse">
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Êtes-vous certain de vouloir refuser cette réservation ?');">Refuser</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-message">
                        <h3>Aucune demande en attente</h3>
                        <p>Toutes les demandes de réservation ont été traitées.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Approved Reservations Section -->
        <div class="section">
            <div class="section-header">
                <div>
                    <h2>Réservations Approuvées</h2>
                    <p class="section-description">Réservations confirmées et actives</p>
                </div>
                <div class="section-badge approved">{{ count($approvedReservations) }}</div>
            </div>

            @if(count($approvedReservations) > 0)
                <div class="reservations-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Ressource</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th>Durée</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approvedReservations as $reservation)
                                <tr>
                                    <td>
                                        <span class="user-name">{{ $reservation->user->name }}</span>
                                        <span class="user-email">{{ $reservation->user->email }}</span>
                                    </td>
                                    <td>{{ $reservation->resource->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservation->start_date)->diffInDays(\Carbon\Carbon::parse($reservation->end_date)) + 1 }}
                                        jour(s)</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-message">
                        <h3>Aucune réservation approuvée</h3>
                        <p>Les réservations approuvées s'afficheront ici.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .manager-container {
            background: #0f1419;
            min-height: 100vh;
            padding: 32px 20px;
        }

        .page-header {
            background: rgba(15, 20, 25, 0.6);
            backdrop-filter: blur(20px);
            padding: 32px;
            margin-bottom: 32px;
            border-radius: 12px;
            border-left: 4px solid #06b6d4;
            border: 1px solid rgba(6, 182, 212, 0.2);
            box-shadow: 0 8px 32px rgba(6, 182, 212, 0.1);
        }

        .header-content h1 {
            font-size: 1.75em;
            color: #ffffff;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .subtitle {
            color: #a0aec0;
            font-size: 0.95em;
            line-height: 1.5;
        }

        .alert {
            display: flex;
            align-items: flex-start;
            padding: 16px 20px;
            margin-bottom: 24px;
            border-radius: 8px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-left: 4px solid #10b981;
            animation: slideDown 0.3s ease-out;
        }

        .alert.alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-left-color: #10b981;
        }

        .alert-content {
            flex: 1;
        }

        .alert-content strong {
            display: block;
            color: #10b981;
            margin-bottom: 4px;
        }

        .alert-content p {
            color: #a0aec0;
            font-size: 0.95em;
        }

        .alert-close {
            background: none;
            border: none;
            font-size: 1.5em;
            color: #a0aec0;
            cursor: pointer;
            margin-left: 16px;
            transition: color 0.2s;
        }

        .alert-close:hover {
            color: #06b6d4;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section {
            background: rgba(20, 30, 50, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(6, 182, 212, 0.1);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            margin-bottom: 32px;
            overflow: hidden;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 28px 32px;
            border-bottom: 1px solid rgba(6, 182, 212, 0.1);
        }

        .section-header h2 {
            font-size: 1.4em;
            color: #ffffff;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .section-description {
            color: #6b7a90;
            font-size: 0.9em;
        }

        .section-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 48px;
            height: 48px;
            border-radius: 8px;
            background: rgba(6, 182, 212, 0.15);
            color: #06b6d4;
            font-weight: 600;
            font-size: 1.1em;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }

        .section-badge.approved {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border-color: rgba(16, 185, 129, 0.3);
        }

        .reservations-list {
            padding: 24px 32px;
        }

        .reservation-item {
            border: 1px solid rgba(6, 182, 212, 0.15);
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 16px;
            transition: all 0.2s;
            background: rgba(15, 20, 25, 0.4);
        }

        .reservation-item:hover {
            border-color: rgba(6, 182, 212, 0.4);
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.1);
        }

        .reservation-item.pending {
            border-left: 3px solid #06b6d4;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .item-main {
            flex: 1;
        }

        .item-title {
            font-size: 1.1em;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .item-subtitle {
            font-size: 0.9em;
            color: #a0aec0;
        }

        .item-status {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .pending-status {
            background: rgba(6, 182, 212, 0.2);
            color: #22d3ee;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-block {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 0.85em;
            color: #6b7a90;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 0.95em;
            color: #ffffff;
            font-weight: 500;
        }

        .justification-box {
            background: rgba(6, 182, 212, 0.08);
            padding: 16px;
            border-radius: 8px;
            border-left: 3px solid #06b6d4;
            margin-bottom: 20px;
            border: 1px solid rgba(6, 182, 212, 0.2);
        }

        .justification-label {
            display: block;
            font-size: 0.85em;
            color: #6b7a90;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .justification-text {
            color: #a0aec0;
            line-height: 1.6;
            font-size: 0.95em;
        }

        .item-footer {
            display: flex;
            gap: 12px;
        }

        .action-form {
            flex: 1;
        }

        .btn {
            width: 100%;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-size: 0.9em;
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

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
            transform: translateY(-2px);
        }

        .reservations-table {
            overflow-x: auto;
        }

        .reservations-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .reservations-table thead {
            background: rgba(6, 182, 212, 0.05);
            border-bottom: 2px solid rgba(6, 182, 212, 0.2);
        }

        .reservations-table th {
            padding: 16px 32px;
            text-align: left;
            font-size: 0.85em;
            font-weight: 600;
            color: #06b6d4;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .reservations-table td {
            padding: 16px 32px;
            border-bottom: 1px solid rgba(6, 182, 212, 0.1);
            color: #a0aec0;
        }

        .reservations-table tbody tr:hover {
            background: rgba(6, 182, 212, 0.05);
        }

        .user-name {
            display: block;
            font-weight: 600;
            margin-bottom: 4px;
            color: #ffffff;
        }

        .user-email {
            display: block;
            font-size: 0.85em;
            color: #6b7a90;
        }

        .empty-state {
            padding: 60px 32px;
            text-align: center;
        }

        .empty-message h3 {
            font-size: 1.2em;
            color: #ffffff;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .empty-message p {
            color: #a0aec0;
            font-size: 0.95em;
        }

        @media (max-width: 768px) {
            .manager-container {
                padding: 16px;
            }

            .page-header {
                padding: 20px;
            }

            .header-content h1 {
                font-size: 1.5em;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .section-header h2 {
                font-size: 1.2em;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .item-footer {
                flex-direction: column;
            }

            .reservations-table th,
            .reservations-table td {
                padding: 12px 16px;
                font-size: 0.85em;
            }
        }

        /* Metrics Grid Styles */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .metric-card {
            background: rgba(20, 30, 50, 0.4);
            backdrop-filter: blur(20px);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-top: 3px solid #06b6d4;
        }

        .metric-value {
            font-size: 2.2em;
            font-weight: 700;
            color: #06b6d4;
            margin-bottom: 4px;
        }

        .metric-label {
            color: #a0aec0;
            font-size: 0.9em;
            margin-bottom: 12px;
        }

        .metric-bar {
            height: 6px;
            background: rgba(6, 182, 212, 0.1);
            border-radius: 3px;
            overflow: hidden;
        }

        .metric-bar.approved {
            background: rgba(16, 185, 129, 0.1);
        }

        .metric-bar.pending {
            background: rgba(249, 115, 22, 0.1);
        }

        .metric-bar.success {
            background: rgba(34, 211, 238, 0.1);
        }

        .metric-fill {
            height: 100%;
            background: linear-gradient(90deg, #06b6d4, #22d3ee);
            border-radius: 3px;
            transition: width 0.8s ease;
        }

        .metric-bar.approved .metric-fill {
            background: #10b981;
        }

        .metric-bar.pending .metric-fill {
            background: #f97316;
        }

        .metric-bar.success .metric-fill {
            background: #22d3ee;
        }

        /* Charts Container */
        .charts-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        .chart-section {
            background: rgba(20, 30, 50, 0.4);
            backdrop-filter: blur(20px);
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(6, 182, 212, 0.1);
        }

        .chart-section.full-width {
            grid-column: 1 / -1;
        }

        .chart-header {
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(6, 182, 212, 0.1);
            padding-bottom: 16px;
        }

        .chart-header h3 {
            font-size: 1.2em;
            color: #ffffff;
            margin: 0 0 4px 0;
        }

        .chart-subtitle {
            color: #6b7a90;
            font-size: 0.85em;
            margin: 0;
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
            margin-top: 16px;
        }

        /* Occupancy Grid */
        .occupancy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }

        .occupancy-item {
            background: rgba(15, 20, 25, 0.4);
            padding: 16px;
            border-radius: 8px;
            border: 1px solid rgba(6, 182, 212, 0.15);
        }

        .occupancy-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .occupancy-name {
            font-weight: 600;
            color: #ffffff;
            font-size: 0.95em;
        }

        .occupancy-value {
            font-weight: 700;
            color: #06b6d4;
            font-size: 1.1em;
        }

        .occupancy-bar {
            height: 8px;
            background: rgba(6, 182, 212, 0.1);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .occupancy-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.6s ease;
        }

        .occupancy-details {
            font-size: 0.8em;
            color: #6b7a90;
        }

        @media (max-width: 768px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .metric-card {
                padding: 16px;
            }

            .chart-wrapper {
                height: 250px;
            }

            .occupancy-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

    <script>
        // Reservation Timeline Chart
        const timelineCtx = document.getElementById('reservationTimelineChart');
        if (timelineCtx) {
            new Chart(timelineCtx, {
                type: 'line',
                data: {
                    labels: @json($reservationTimeline['labels']),
                    datasets: [{
                        label: 'Nouvelles Réservations',
                        data: @json($reservationTimeline['data']),
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#06b6d4',
                        pointBorderColor: '#0f1419',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: { size: 12 },
                                color: '#a0aec0',
                            }
                        },
                        grid: {
                            display: true,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#6b7a90',
                                font: { size: 11 }
                            },
                            grid: {
                                color: 'rgba(6, 182, 212, 0.05)',
                                drawBorder: false,
                            }
                        },
                        x: {
                            ticks: {
                                color: '#6b7a90',
                                font: { size: 11 }
                            },
                            grid: {
                                display: false,
                                drawBorder: false,
                            }
                        }
                    }
                }
            });
        }
    </script>

@endsection