<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des Incidents</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <style>
        body {
            background-color: #0f1419 !important;
            color: #ffffff !important;
        }

        .page-wrapper {
            background-color: #0f1419;
        }

        .main-content {
            background-color: #0f1419;
        }

        .page-title {
            color: #ffffff;
            font-size: 2em;
            margin-bottom: 1.5em;
        }

        .status-select {
            padding: 8px 12px;
            border: 1px solid rgba(6, 182, 212, 0.15);
            border-radius: 6px;
            font-size: 0.9em;
            cursor: pointer;
            background-color: rgba(20, 30, 50, 0.6);
            color: #ffffff;
        }

        .status-select:focus {
            outline: none;
            border-color: #06b6d4;
            box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2);
        }

        .status-ouvert {
            color: #fca5a5;
            font-weight: 600;
        }

        .status-en_cours {
            color: #fbbf24;
            font-weight: 600;
        }

        .status-resolu {
            color: #6ee7b7;
            font-weight: 600;
        }

        .user-info {
            font-size: 0.9em;
            color: #6b7a90;
        }

        .table-container {
            background-color: rgba(20, 30, 50, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.15);
            border-radius: 8px;
            padding: 1.5rem;
            backdrop-filter: blur(20px);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            color: #ffffff;
        }

        .data-table thead {
            background-color: rgba(6, 182, 212, 0.1);
            border-bottom: 2px solid rgba(6, 182, 212, 0.15);
        }

        .data-table th {
            padding: 1rem;
            text-align: left;
            color: #06b6d4;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(6, 182, 212, 0.15);
            color: #ffffff;
        }

        .data-table tbody tr:hover {
            background-color: rgba(6, 182, 212, 0.05);
        }

        .btn-details {
            padding: 8px 16px;
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }

        .btn-details:hover {
            box-shadow: 0 0 12px rgba(6, 182, 212, 0.4);
            transform: translateY(-2px);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 999999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background-color: rgba(20, 30, 50, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.15);
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .modal-header {
            font-size: 1.5em;
            font-weight: 600;
            margin-bottom: 15px;
            color: #06b6d4;
        }

        .modal-close {
            float: right;
            font-size: 1.8em;
            cursor: pointer;
            color: #a0aec0;
            transition: color 0.3s ease;
        }

        .modal-close:hover {
            color: #06b6d4;
        }

        .modal-body {
            margin-bottom: 20px;
            line-height: 1.6;
            color: #a0aec0;
        }

        .modal-body p {
            margin-bottom: 12px;
        }

        .modal-body strong {
            color: #ffffff;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-modal {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95em;
            transition: all 0.3s ease;
        }

        .btn-close {
            background-color: #6b7a90;
            color: #ffffff;
        }

        .btn-close:hover {
            background-color: #a0aec0;
        }

        .btn-submit {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
        }

        .btn-submit:hover {
            box-shadow: 0 0 12px rgba(6, 182, 212, 0.4);
        }
    </style>
</head>

<body>

    @include('stats.sidebar')

    <div class="page-wrapper">
        @include('partials.navbar')

        <div class="main-content">
            <h1 class="page-title">Gestion des Incidents</h1>

            @if(session('success'))
                <div
                    style="background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; padding: 15px; margin-bottom: 20px; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.3);">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Ressource</th>
                            <th>Titre</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incidents as $incident)
                            <tr>
                                <td>
                                    <strong>{{ $incident->user->name ?? 'N/A' }}</strong>
                                    <div class="user-info">{{ $incident->user->email ?? '' }}</div>
                                </td>
                                <td><strong>{{ $incident->resource->name ?? 'Ressource supprimée' }}</strong></td>
                                <td>{{ $incident->title }}</td>
                                <td>{{ $incident->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="status-{{ $incident->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn-details" onclick="openModal('{{ $incident->id }}');">
                                        Détails & Gérer
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal pour chaque incident -->
                            <div id="modal-{{ $incident->id }}" class="modal">
                                <div class="modal-content">
                                    <span class="modal-close" onclick="closeModal('{{ $incident->id }}');">&times;</span>
                                    <div class="modal-header">Détails de l'Incident #{{ $incident->id }}</div>

                                    <div class="modal-body">
                                        <p><strong>Utilisateur:</strong> {{ $incident->user->name ?? 'N/A' }}
                                            ({{ $incident->user->email ?? '' }})</p>
                                        <p><strong>Ressource:</strong>
                                            {{ $incident->resource->name ?? 'Ressource supprimée' }}</p>
                                        <p><strong>Titre:</strong> {{ $incident->title }}</p>
                                        <p><strong>Description:</strong></p>
                                        <p
                                            style="background-color: rgba(6, 182, 212, 0.1); padding: 10px; border-radius: 6px; border-left: 3px solid #06b6d4;">
                                            {{ $incident->description }}
                                        </p>
                                        <p><strong>Priorité:</strong>
                                            <span
                                                style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 0.9em;
                                                background: {{ $incident->priority === 'critical' ? 'rgba(239, 68, 68, 0.15)' : ($incident->priority === 'high' ? 'rgba(245, 158, 11, 0.15)' : ($incident->priority === 'medium' ? 'rgba(59, 130, 246, 0.15)' : 'rgba(6, 182, 212, 0.15)')) }};
                                                color: {{ $incident->priority === 'critical' ? '#fca5a5' : ($incident->priority === 'high' ? '#fbbf24' : ($incident->priority === 'medium' ? '#60a5fa' : '#22d3ee')) }};">
                                                {{ $incident->priority ? ucfirst($incident->priority) : 'N/A' }}
                                            </span>
                                        </p>
                                        <p><strong>Date:</strong> {{ $incident->created_at->format('d/m/Y H:i') }}</p>
                                        <p><strong>Statut actuel:</strong> <span
                                                class="status-{{ $incident->status }}">{{ ucfirst(str_replace('_', ' ', $incident->status)) }}</span>
                                        </p>
                                    </div>

                                    <form action="{{ route('incidents.updateStatus', $incident->id) }}" method="POST"
                                        style="margin-bottom: 20px;">
                                        @csrf
                                        @method('PATCH')
                                        <div style="margin-bottom: 15px;">
                                            <label
                                                style="display: block; margin-bottom: 8px; font-weight: 600; color: #ffffff;">Changer
                                                le statut:</label>
                                            <select name="status" class="status-select" style="width: 100%;">
                                                <option value="ouvert" {{ $incident->status == 'ouvert' ? 'selected' : '' }}>
                                                    Ouvert</option>
                                                <option value="en_cours" {{ $incident->status == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                                <option value="resolu" {{ $incident->status == 'resolu' ? 'selected' : '' }}>
                                                    Résolu</option>
                                            </select>
                                        </div>
                                        <div class="modal-buttons">
                                            <button type="button" class="btn-modal btn-close"
                                                onclick="closeModal('{{ $incident->id }}');">Fermer</button>
                                            <button type="submit" class="btn-modal btn-submit">
                                                Mettre à jour
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #7f8c8d; padding: 20px;">Aucun incident
                                    signalé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- End page-wrapper -->

    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        function openModal(incidentId) {
            document.getElementById('modal-' + incidentId).style.display = 'block';
        }

        function closeModal(incidentId) {
            document.getElementById('modal-' + incidentId).style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>

    <script src="{{ asset('js/main.js') }}"></script>
</body>

</html>