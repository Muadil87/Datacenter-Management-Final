<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Journal d'audit</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #0f1419;
            color: #ffffff;
        }

        .main-content {
            background: #0f1419;
        }

        .page-title {
            color: #ffffff;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .table-container {
            background: rgba(20, 30, 50, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.15);
            border-radius: 12px;
            padding: 28px;
            backdrop-filter: blur(20px);
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: rgba(6, 182, 212, 0.08);
        }

        .data-table th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            color: #06b6d4;
            font-size: 0.9em;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(6, 182, 212, 0.15);
        }

        .data-table td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(6, 182, 212, 0.05);
            color: #a0aec0;
        }

        .data-table tbody tr:hover {
            background: rgba(6, 182, 212, 0.05);
        }

        .user-badge {
            display: inline-block;
            padding: 6px 14px;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.2) 0%, rgba(34, 211, 238, 0.1) 100%);
            color: #06b6d4;
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 6px;
            font-size: 0.9em;
            font-weight: 600;
        }

        .user-badge.system {
            background: rgba(107, 122, 144, 0.2);
            color: #a0aec0;
            border-color: rgba(107, 122, 144, 0.3);
        }

        .text-muted {
            color: #6b7a90;
            font-size: 0.9em;
        }

        .action-badge {
            display: inline-block;
            padding: 6px 12px;
            background: rgba(6, 182, 212, 0.1);
            color: #06b6d4;
            border-radius: 6px;
            font-size: 0.85em;
            font-weight: 600;
        }
    </style>
</head>

<body>

    @include('stats.sidebar')

    <div class="page-wrapper">
        @include('partials.navbar')

        <div class="main-content">
            <h1 class="page-title">Journal d'audit</h1>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Horodatage</th>
                            <th>Utilisateur</th>
                            <th>Action</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td class="text-muted">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    @if($log->user)
                                        <span class="user-badge">{{ $log->user->name }}</span>
                                    @else
                                        <span class="user-badge system">Système</span>
                                    @endif
                                </td>
                                <td><span class="action-badge">{{ $log->action }}</span></td>
                                <td>{{ $log->details }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/main.js') }}"></script>
</body>

</html>