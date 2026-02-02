@extends('layout')

@section('content')
    <style>
        .dashboard-container {
            max-width: none;
            width: 100%;
            margin: 0;
            padding: 20px 0;
            background: #0f1419;
            min-height: 100vh;
        }

        .dashboard-header {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.15) 0%, rgba(34, 211, 238, 0.1) 100%);
            backdrop-filter: blur(20px);
            color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 40px;
            border: 1px solid rgba(6, 182, 212, 0.2);
        }

        .dashboard-header h1 {
            margin: 0 0 10px 0;
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .dashboard-header p {
            margin: 0;
            color: #a0aec0;
            font-size: 16px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .action-btn {
            background: rgba(20, 30, 50, 0.5);
            backdrop-filter: blur(20px);
            padding: 24px;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
            border: 1px solid rgba(6, 182, 212, 0.15);
            transform: translateZ(0);
            backface-visibility: hidden;
            will-change: transform;
        }

        .action-btn:hover {
            background: rgba(20, 30, 50, 0.8);
            border-color: rgba(6, 182, 212, 0.4);
            box-shadow: 0 8px 30px rgba(6, 182, 212, 0.15);
            transform: translateY(-4px);
        }

        .action-icon {
            font-size: 2.5em;
            margin-bottom: 12px;
            backface-visibility: hidden;
        }

        .action-title {
            font-weight: 600;
            color: #ffffff;
            margin: 8px 0;
            font-size: 16px;
            backface-visibility: hidden;
        }

        .action-desc {
            font-size: 0.9em;
            color: #a0aec0;
            backface-visibility: hidden;
        }

        /* Metrics Section */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .metric-card {
            background: rgba(20, 30, 50, 0.5);
            backdrop-filter: blur(20px);
            padding: 24px;
            border-radius: 12px;
            border: 1px solid rgba(6, 182, 212, 0.15);
            text-align: center;
            transition: all 0.3s;
        }

        .metric-card:hover {
            border-color: rgba(6, 182, 212, 0.3);
            box-shadow: 0 8px 30px rgba(6, 182, 212, 0.1);
        }

        .metric-value {
            font-size: 2.5em;
            font-weight: 700;
            background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .metric-label {
            color: #a0aec0;
            font-size: 0.95em;
            font-weight: 500;
        }

        /* Charts Container */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }

        .chart-section {
            background: rgba(20, 30, 50, 0.5);
            backdrop-filter: blur(20px);
            padding: 28px;
            border-radius: 12px;
            border: 1px solid rgba(6, 182, 212, 0.15);
        }

        .chart-header {
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(6, 182, 212, 0.1);
            padding-bottom: 16px;
        }

        .chart-header h3 {
            margin: 0 0 6px 0;
            font-size: 1.2em;
            color: #ffffff;
            font-weight: 600;
        }

        .chart-subtitle {
            margin: 0;
            font-size: 0.9em;
            color: #a0aec0;
        }

        .chart-wrapper {
            position: relative;
            height: 280px;
        }

        /* Resource Grid */
        .resources-section {
            background: rgba(20, 30, 50, 0.5);
            backdrop-filter: blur(20px);
            padding: 28px;
            border-radius: 12px;
            border: 1px solid rgba(6, 182, 212, 0.15);
        }

        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .resource-card {
            background: rgba(15, 20, 25, 0.5);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 8px;
            padding: 16px;
            transition: all 0.3s;
        }

        .resource-card:hover {
            border-color: rgba(6, 182, 212, 0.4);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.15);
        }

        .resource-name {
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .resource-category {
            display: inline-block;
            background: rgba(6, 182, 212, 0.2);
            color: #06b6d4;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8em;
            font-weight: 600;
            margin-bottom: 10px;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }

        .resource-state {
            font-size: 0.9em;
            color: #a0aec0;
            margin-bottom: 12px;
        }

        .reserve-btn {
            display: inline-block;
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85em;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .reserve-btn:hover {
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
            transform: translateY(-2px);
        }

        .section-title {
            color: #ffffff;
            margin-bottom: 20px;
            font-size: 1.3em;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }

            .metric-card {
                padding: 16px;
            }

            .quick-actions {
                grid-template-columns: 1fr;
            }

            .resources-grid {
                grid-template-columns: 1fr;
            }

            .chart-wrapper {
                height: 250px;
            }

            .dashboard-header h1 {
                font-size: 2em;
            }
        }
    </style>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1><i class="fas fa-hand-wave"></i> Bienvenue , {{ Auth::user()->name }}!</h1>
            <p>Tableau de bord utilisateur - Gestion et suivi de vos ressources</p>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="{{ route('resources.index') }}" class="action-btn">
                <div class="action-icon"><i class="fas fa-list"></i></div>
                <div class="action-title">Toutes Ressources</div>
                <div class="action-desc">Parcourir & réserver</div>
            </a>
            <a href="{{ route('internal.reservations') }}" class="action-btn">
                <div class="action-icon"><i class="fas fa-file-alt"></i></div>
                <div class="action-title">Mes Réservations</div>
                <div class="action-desc">Historique complet</div>
            </a>
            <a href="{{ route('internal.incidents.my') }}" class="action-btn">
                <div class="action-icon"><i class="fas fa-chart-bar"></i></div>
                <div class="action-title">Mes Incidents</div>
                <div class="action-desc">Suivi des rapports</div>
            </a>
            <a href="{{ route('profile') }}" class="action-btn">
                <div class="action-icon"><i class="fas fa-cog"></i></div>
                <div class="action-title">Paramètres</div>
                <div class="action-desc">Mon profil</div>
            </a>
        </div>

        <!-- Utilization Metrics -->
        <div style="margin-bottom: 40px;">
            <h2 class="section-title"><i class="fas fa-chart-bar"></i> Mes Statistiques d'Utilisation</h2>
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-value">{{ $utilizationMetrics['total'] }}</div>
                    <div class="metric-label">Réservations</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ $utilizationMetrics['approved'] }}</div>
                    <div class="metric-label">Approuvées</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ $utilizationMetrics['pending'] }}</div>
                    <div class="metric-label">En Attente</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ $utilizationMetrics['finished'] }}</div>
                    <div class="metric-label">Terminées</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-grid">
            <!-- Status Breakdown Pie Chart -->
            <div class="chart-section">
                <div class="chart-header">
                    <h3><i class="fas fa-map-pin"></i> Répartition des Réservations</h3>
                    <p class="chart-subtitle">État actuel de vos demandes</p>
                </div>
                <div class="chart-wrapper">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Activity Timeline Chart -->
            <div class="chart-section">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-line"></i> Activité (12 derniers mois)</h3>
                    <p class="chart-subtitle">Tendance de vos réservations</p>
                </div>
                <div class="chart-wrapper">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

    <script>
        // Status Breakdown Pie Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($statusBreakdown['labels']),
                    datasets: [{
                        data: @json($statusBreakdown['data']),
                        backgroundColor: [
                            'rgba(6, 182, 212, 0.6)',
                            'rgba(16, 185, 129, 0.6)',
                            'rgba(251, 191, 36, 0.6)',
                            'rgba(239, 68, 68, 0.6)'
                        ],
                        borderColor: '#0f1419',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { size: 12 },
                                color: '#a0aec0',
                                padding: 16
                            }
                        }
                    }
                }
            });
        }

        // Activity Timeline Line Chart
        const activityCtx = document.getElementById('activityChart');
        if (activityCtx) {
            new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: @json($activityTimeline['labels']),
                    datasets: [{
                        label: 'Réservations créées',
                        data: @json($activityTimeline['data']),
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#06b6d4',
                        pointBorderColor: '#0f1419',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
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
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#a0aec0',
                                font: { size: 11 }
                            },
                            grid: {
                                color: 'rgba(6, 182, 212, 0.05)',
                                drawBorder: false,
                            }
                        },
                        x: {
                            ticks: {
                                color: '#a0aec0',
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