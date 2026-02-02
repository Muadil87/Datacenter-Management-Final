<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f5f6f8;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        .page-title {
            font-size: 2em;
            color: #2c3e50;
            margin-bottom: 32px;
            font-weight: 600;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #3498db;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .stat-card:nth-child(2) {
            border-left-color: #27ae60;
        }

        .stat-card:nth-child(3) {
            border-left-color: #e67e22;
        }

        .stat-card:nth-child(4) {
            border-left-color: #e74c3c;
        }

        .stat-title {
            font-size: 0.85em;
            color: #7f8c8d;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 2em;
            font-weight: 700;
            color: #2c3e50;
        }

        .bottom-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        .content-box {
            background: white;
            padding: 28px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .box-title {
            font-size: 1.3em;
            color: #2c3e50;
            margin-bottom: 24px;
            font-weight: 600;
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 16px;
        }

        .graph-area {
            display: flex;
            gap: 40px;
            align-items: center;
            justify-content: space-around;
            padding: 32px 0;
        }

        .bar-group {
            text-align: center;
        }

        .bar-percent {
            font-size: 2.2em;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 12px;
        }

        .bar {
            width: 100px;
            border-radius: 4px;
            margin: 0 auto 12px;
        }

        .bar-red {
            background: linear-gradient(180deg, #e74c3c 0%, #c0392b 100%);
        }

        .bar-green {
            background: linear-gradient(180deg, #27ae60 0%, #229954 100%);
        }

        .bar-label {
            font-size: 0.9em;
            color: #7f8c8d;
            font-weight: 600;
        }

        .progress-group {
            margin-bottom: 28px;
        }

        .progress-labels {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.95em;
        }

        .progress-labels span:first-child {
            color: #2c3e50;
            font-weight: 600;
        }

        .progress-labels span:last-child {
            color: #3498db;
            font-weight: 700;
        }

        .progress-track {
            height: 8px;
            background: #ecf0f1;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3498db 0%, #2980b9 100%);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .top-res-box {
            background: #f9f9fb;
            padding: 20px;
            border-radius: 6px;
            border-left: 3px solid #3498db;
        }

        .top-res-label {
            font-size: 0.85em;
            color: #7f8c8d;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .top-res-name {
            font-size: 1.2em;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .top-res-count {
            font-size: 0.9em;
            color: #7f8c8d;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: #f9f9fb;
            border-bottom: 2px solid #ecf0f1;
        }

        .data-table th {
            padding: 16px;
            text-align: left;
            font-size: 0.85em;
            font-weight: 600;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 16px;
            border-bottom: 1px solid #ecf0f1;
            color: #2c3e50;
        }

        .data-table tbody tr:hover {
            background: #f9f9fb;
        }

        .role-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 4px;
            font-size: 0.85em;
            text-transform: capitalize;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .status-active {
            background: #edf9f5;
            color: #27ae60;
        }

        .status-inactive {
            background: #fef5f5;
            color: #e74c3c;
        }

        .btn-action {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-action:hover {
            opacity: 0.9;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        }

        .btn-activate {
            background: #27ae60;
            color: white;
        }

        .btn-deactivate {
            background: #e74c3c;
            color: white;
        }

        @media (max-width: 1024px) {
            .bottom-section {
                grid-template-columns: 1fr;
            }

            .graph-area {
                flex-direction: column;
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 16px;
            }

            .page-title {
                font-size: 1.5em;
            }

            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .data-table {
                font-size: 0.85em;
            }

            .data-table th,
            .data-table td {
                padding: 12px;
            }
        }
    </style>
</head>

<body>

    @include('stats.sidebar')

    <div class="page-wrapper">
        @include('partials.navbar')

        <div class="main-content">
            <h1 class="page-title">Tableau de Bord</h1>

            <!-- 1. Les Cartes du haut -->
            <div class="stats-container">
                <div class="stat-card">
                    <span class="stat-title">Total Incidents</span>
                    <span class="stat-value">{{ $totalIncidents }}</span>
                </div>

                <div class="stat-card">
                    <span class="stat-title">Résolus</span>
                    <span class="stat-value">{{ $incidentsResolu }}</span>
                </div>

                <div class="stat-card">
                    <span class="stat-title">En cours</span>
                    <span class="stat-value">{{ $incidentsEnCours }}</span>
                </div>

                <div class="stat-card">
                    <span class="stat-title">Logs Système</span>
                    <span class="stat-value">{{ $totalLogs }}</span>
                </div>
            </div>

            <!-- 2. La Section du bas (Graphique + Ressources) -->
            <div class="bottom-section">

                <!-- A. Le Graphique (Gauche) -->
                <div class="content-box">
                    <div class="box-title">Répartition des Incidents</div>

                    <div class="graph-area">
                        <div class="bar-group">
                            <div class="bar-percent">{{ round($percentOuvert) }}%</div>
                            <div class="bar bar-red" style="height: {{ max(50, round($percentOuvert) * 2) }}px;"></div>
                            <div class="bar-label">Ouverts</div>
                        </div>

                        <div class="bar-group">
                            <div class="bar-percent">{{ round($percentResolu) }}%</div>
                            <div class="bar bar-green" style="height: {{ max(50, round($percentResolu) * 2) }}px;">
                            </div>
                            <div class="bar-label">Résolus</div>
                        </div>
                    </div>
                </div>

                <!-- B. Les Stats Ressources (Droite) -->
                <div class="content-box">
                    <div class="box-title">Performance Ressources</div>

                    <!-- Taux d'occupation -->
                    <div class="progress-group">
                        <div class="progress-labels">
                            <span>Taux d'occupation</span>
                            <span>{{ number_format($tauxOccupation, 2) }}%</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ min(100, round($tauxOccupation)) }}%;"></div>
                        </div>
                    </div>

                    <!-- Ressource la plus utilisée -->
                    <div class="top-res-box">
                        <div class="top-res-label">Ressource la plus utilisée</div>
                        <div class="top-res-name">{{ $topRessourceName }}</div>
                        <div class="top-res-count">
                            {{ $topRessourceCount }} réservations ce mois-ci
                        </div>
                    </div>
                </div>

            </div> <!-- Fin bottom-section -->

            <!-- 4. Stats Cards Section -->
            <div class="stats-container">
                <div class="stat-card">
                    <span class="stat-title">Utilisateurs Totaux</span>
                    <span class="stat-value">{{ $totalUsers ?? 0 }}</span>
                </div>

                <div class="stat-card">
                    <span class="stat-title">Ressources Totales</span>
                    <span class="stat-value">{{ $totalResources ?? 0 }}</span>
                </div>

                <div class="stat-card">
                    <span class="stat-title">En Utilisation</span>
                    <span class="stat-value">{{ $resourcesOccupied ?? 0 }}</span>
                </div>

                <div class="stat-card">
                    <span class="stat-title">Maintenance</span>
                    <span class="stat-value">{{ $resourcesMaintenance ?? 0 }}</span>
                </div>
            </div>
        </div>

    </div>
    </div> <!-- End page-wrapper -->

    <script src="{{ asset('js/main.js') }}"></script>
</body>

</html>