@extends('layout')

@section('content')
    <style>
        :root {
            --bg-dark: #0f1419;
            --bg-card: rgba(20, 30, 50, 0.6);
            --border-card: rgba(255, 255, 255, 0.08);
            --text-primary: #ffffff;
            --text-secondary: #a0aec0;
            --text-tertiary: #6b7a90;
            --accent: #06b6d4;
            --accent-light: #22d3ee;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
        }

        body {
            background: var(--bg-dark);
            color: var(--text-primary);
        }

        .admin-container {
            max-width: none;
            width: 100%;
            margin: 0;
            padding: 20px 0;
        }

        .page-header {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(8, 145, 178, 0.1) 100%);
            border: 1px solid var(--border-card);
            color: var(--text-primary);
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }

        .header-content h1 {
            margin: 0 0 10px 0;
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-content .subtitle {
            margin: 0;
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* Alert Styles */
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(16, 185, 129, 0.3);
            background: rgba(16, 185, 129, 0.1);
        }

        .alert.alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .alert-close {
            background: none;
            border: none;
            color: inherit;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.7;
        }

        .alert-close:hover {
            opacity: 1;
        }

        /* Metrics Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .metric-card {
            background: var(--bg-card);
            padding: 25px;
            border-radius: 16px;
            border: 1px solid var(--border-card);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
            backdrop-filter: blur(20px);
        }

        .metric-card:hover {
            border-color: var(--accent);
            box-shadow: 0 25px 50px -12px rgba(6, 182, 212, 0.3);
            transform: translateY(-2px);
        }

        .metric-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent-light);
            margin-bottom: 10px;
        }

        .metric-label {
            font-size: 0.95rem;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 12px;
        }

        .metric-bar {
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            overflow: hidden;
        }

        .metric-bar .metric-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent) 0%, var(--accent-light) 100%);
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-container {
            background: var(--bg-card);
            padding: 25px;
            border-radius: 16px;
            border: 1px solid var(--border-card);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(20px);
        }

        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
        }

        /* Status Card */
        .status-card {
            background: transparent;
            padding: 0;
        }

        .status-card h3 {
            margin: 0 0 15px 0;
            font-size: 1rem;
            color: var(--text-primary);
            font-weight: 600;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-label {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .status-value {
            font-weight: 600;
            color: var(--accent-light);
        }

        /* Users Table */
        .users-section {
            background: var(--bg-card);
            padding: 25px;
            border-radius: 16px;
            border: 1px solid var(--border-card);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            margin-bottom: 30px;
            backdrop-filter: blur(20px);
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table thead {
            background: rgba(6, 182, 212, 0.05);
        }

        .users-table th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            color: var(--accent-light);
            border-bottom: 2px solid var(--border-card);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .users-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-card);
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .users-table tbody tr {
            transition: all 0.2s ease;
        }

        .users-table tbody tr:hover {
            background: rgba(6, 182, 212, 0.05);
            border-left: 3px solid var(--accent);
            padding-left: 3px;
        }

        .users-table strong {
            color: var(--text-primary);
        }

        .badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge.admin {
            background: rgba(6, 182, 212, 0.15);
            color: var(--accent-light);
            border: 1px solid rgba(6, 182, 212, 0.3);
        }

        .badge.manager {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .badge.internal {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge.active {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge.inactive {
            background: rgba(107, 114, 144, 0.15);
            color: var(--text-tertiary);
            border: 1px solid rgba(107, 114, 144, 0.3);
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 30px;
        }

        .action-btn {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            color: #0a0e17;
            padding: 12px 16px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px -10px var(--accent);
        }

        /* Stat Cards */
        .stat-card {
            background: var(--bg-card);
            padding: 24px;
            border-radius: 16px;
            border: 1px solid var(--border-card);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(20px);
            border-top: 4px solid var(--accent);
        }

        .stat-card.success {
            border-top-color: var(--success);
        }

        .stat-card.warning {
            border-top-color: var(--warning);
        }

        .stat-label {
            font-size: 0.85em;
            color: var(--text-tertiary);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .stat-value {
            font-size: 2.2em;
            font-weight: 700;
            color: var(--accent-light);
        }

        /* Responsive */
        @media (max-width: 768px) {

            .metrics-grid,
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .page-header {
                padding: 20px;
            }

            .header-content h1 {
                font-size: 1.6rem;
            }

            .metric-value {
                font-size: 2rem;
            }

            .users-table {
                font-size: 0.9rem;
            }

            .users-table th,
            .users-table td {
                padding: 8px;
            }
        }
    </style>

    <div class="admin-container">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-wrench"></i> Tableau de bord Admin</h1>
                <p class="subtitle">Vue d'ensemble du système et gestion des utilisateurs</p>
            </div>
        </div>

        <div class="quick-actions">
            <a href="{{ route('admin.maintenance.calendar') }}" class="action-btn">
                <i class="fas fa-calendar"></i> Calendrier de maintenance
            </a>
            <a href="{{ route('admin.incidents.history') }}" class="action-btn">
                <i class="fas fa-chart-bar"></i> Historique des incidents
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

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-value">{{ $totalUsers }}</div>
                <div class="metric-label">Total Utilisateurs</div>
                <div class="metric-bar">
                    <div class="metric-fill" style="width: 100%;"></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-value">{{ $totalResources }}</div>
                <div class="metric-label">Ressources</div>
                <div class="metric-bar">
                    <div class="metric-fill" style="width: 100%;"></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-value">{{ $resourcesOccupied }}</div>
                <div class="metric-label">Ressources occupées</div>
                <div class="metric-bar">
                    <div class="metric-fill"
                        style="width: {{ $totalResources > 0 ? min(($resourcesOccupied / $totalResources) * 100, 100) : 0 }}%;">
                    </div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-value">{{ $resourcesMaintenance }}</div>
                <div class="metric-label">En maintenance</div>
                <div class="metric-bar">
                    <div class="metric-fill"
                        style="width: {{ $totalResources > 0 ? min(($resourcesMaintenance / $totalResources) * 100, 100) : 0 }}%;">
                    </div>
                </div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-container">
                <div class="chart-title"><i class="fas fa-chart-pie"></i> État des ressources</div>
                <div class="chart-wrapper">
                    <canvas id="resourceStatusChart"></canvas>
                </div>
            </div>

            <div class="chart-container">
                <div class="chart-title"><i class="fas fa-users"></i> Répartition des rôles</div>
                <div class="chart-wrapper">
                    <canvas id="userRoleChart"></canvas>
                </div>
            </div>

            <div class="chart-container">
                <div class="chart-title"><i class="fas fa-heart"></i> Santé du système</div>
                <div class="status-card">
                    <div class="status-item">
                        <span class="status-label">Ressources disponibles</span>
                        <span class="status-value">{{ $totalResources - $resourcesOccupied - $resourcesMaintenance }}</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Taux d'occupation</span>
                        <span
                            class="status-value">{{ $totalResources > 0 ? round(($resourcesOccupied / $totalResources) * 100) : 0 }}%</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Taux de maintenance</span>
                        <span
                            class="status-value">{{ $totalResources > 0 ? round(($resourcesMaintenance / $totalResources) * 100) : 0 }}%</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Utilisateurs actifs</span>
                        <span class="status-value">{{ $users->where('is_active', true)->count() }}</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Total incidents</span>
                        <span class="status-value">{{ $totalIncidents }}</span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Logs système</span>
                        <span class="status-value">{{ $totalLogs }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 32px;">
            <div class="stat-card">
                <div class="stat-label">Total incidents</div>
                <div class="stat-value">{{ $totalIncidents }}</div>
            </div>

            <div class="stat-card success">
                <div class="stat-label">Résolus</div>
                <div class="stat-value">{{ $incidentsResolu }}</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-label">En cours</div>
                <div class="stat-value">{{ $incidentsEnCours }}</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
            <div class="chart-container">
                <div class="chart-title">Répartition des incidents</div>

                <div style="display: flex; gap: 40px; align-items: center; justify-content: space-around; padding: 32px 0;">
                    <div style="text-align: center;">
                        <div style="font-size: 2.2em; font-weight: 700; color: var(--accent-light); margin-bottom: 12px;">
                            {{ round($percentOuvert) }}%
                        </div>
                        <div
                            style="width: 100px; height: {{ max(50, round($percentOuvert) * 2) }}px; background: linear-gradient(180deg, var(--error) 0%, #dc2626 100%); border-radius: 4px; margin: 0 auto 12px;">
                        </div>
                        <div style="font-size: 0.9em; color: var(--text-secondary); font-weight: 600;">Ouverts</div>
                    </div>

                    <div style="text-align: center;">
                        <div style="font-size: 2.2em; font-weight: 700; color: var(--accent-light); margin-bottom: 12px;">
                            {{ round($percentResolu) }}%
                        </div>
                        <div
                            style="width: 100px; height: {{ max(50, round($percentResolu) * 2) }}px; background: linear-gradient(180deg, var(--success) 0%, #059669 100%); border-radius: 4px; margin: 0 auto 12px;">
                        </div>
                        <div style="font-size: 0.9em; color: var(--text-secondary); font-weight: 600;">Résolus</div>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <div class="chart-title">Performance des ressources</div>

                <div style="margin-bottom: 28px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.95em;">
                        <span style="color: var(--text-primary); font-weight: 600;">Taux d'occupation</span>
                        <span
                            style="color: var(--accent-light); font-weight: 700;">{{ number_format($tauxOccupation, 2) }}%</span>
                    </div>
                    <div style="height: 8px; background: rgba(255, 255, 255, 0.1); border-radius: 4px; overflow: hidden;">
                        <div
                            style="height: 100%; background: linear-gradient(90deg, var(--accent) 0%, var(--accent-light) 100%); border-radius: 4px; transition: width 0.3s ease; width: {{ min(100, round($tauxOccupation)) }}%;">
                        </div>
                    </div>
                </div>

                <div
                    style="background: rgba(6, 182, 212, 0.1); padding: 20px; border-radius: 6px; border-left: 3px solid var(--accent);">
                    <div
                        style="font-size: 0.85em; color: var(--text-tertiary); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 8px;">
                        Ressource la plus utilisée</div>
                    <div style="font-size: 1.2em; color: var(--text-primary); font-weight: 600; margin-bottom: 6px;">
                        {{ $topRessourceName }}
                    </div>
                    <div style="font-size: 0.9em; color: var(--text-secondary);">{{ $topRessourceCount }} réservations ce mois-ci</div>
                </div>
            </div>
        </div>

        </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        // Chart colors for dark theme
        const chartColors = {
            primary: '#06b6d4',
            success: '#10b981',
            warning: '#f59e0b',
            error: '#ef4444',
            secondary: '#a0aec0'
        };

        // Resource Status Chart (Doughnut)
        const resourceStatusCtx = document.getElementById('resourceStatusChart')?.getContext('2d');
        if (resourceStatusCtx) {
            new Chart(resourceStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Disponibles', 'Occupées', 'Maintenance'],
                    datasets: [{
                        data: [
                                {{ $totalResources - $resourcesOccupied - $resourcesMaintenance }},
                                {{ $resourcesOccupied }},
                            {{ $resourcesMaintenance }}
                        ],
                        backgroundColor: [chartColors.success, chartColors.warning, chartColors.error],
                        borderColor: '#0f1419',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 12 },
                                color: chartColors.secondary
                            }
                        }
                    }
                }
            });
        }

        // User Role Distribution (Pie)
        const userRoleCtx = document.getElementById('userRoleChart')?.getContext('2d');
        if (userRoleCtx) {
            new Chart(userRoleCtx, {
                type: 'pie',
                data: {
                    labels: ['Administrateurs', 'Gestionnaires', 'Utilisateurs'],
                    datasets: [{
                        data: [
                            @php
                                echo collect($users)->where('role', 'admin')->count() . ',';
                                echo collect($users)->where('role', 'manager')->count() . ',';
                                echo collect($users)->where('role', 'internal')->count();
                            @endphp
                        ],
                        backgroundColor: [chartColors.primary, '#3b82f6', chartColors.success],
                        borderColor: '#0f1419',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position:'bottom',
                            labels: { 
                                padding: 15, 
                                font: { size: 12 },
                                color: chartColors.secondary
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection