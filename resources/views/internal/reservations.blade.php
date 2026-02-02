@extends('layouts.app')

@section('content')
    <style>
        body {
            background: #0f1419;
            color: #ffffff;
        }

        /* Structure */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .header-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-box h1 {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }

        /* --- NEW: FILTER BAR STYLES --- */
        .filter-bar {
            background: rgba(20, 30, 50, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(6, 182, 212, 0.15);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-form {
            display: flex;
            gap: 15px;
            width: 100%;
            flex-wrap: wrap;
        }

        .input-dark {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(6, 182, 212, 0.15);
            color: #ffffff;
            padding: 12px 16px;
            border-radius: 8px;
            outline: none;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .input-dark:focus {
            border-color: #06b6d4;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.15);
            background: rgba(255, 255, 255, 0.08);
        }

        .input-search {
            flex-grow: 1;
            min-width: 200px;
        }

        .input-select {
            min-width: 180px;
            cursor: pointer;
            /* Custom arrow for dark mode */
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23a0aec0' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
            padding-right: 2.5rem;
        }

        .input-select option {
            background: #15202b;
            /* Solid dark background for dropdown options */
            color: white;
        }

        .btn-filter {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-filter:hover {
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
            transform: translateY(-1px);
        }

        .btn-reset {
            color: #a0aec0;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            transition: color 0.3s;
            margin-left: 10px;
        }

        .btn-reset:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        /* --- END FILTER STYLES --- */

        /* Bouton Nouvelle Réservation */
        .btn-new {
            text-decoration: none;
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.95em;
            transition: all 0.3s;
        }

        .btn-new:hover {
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
            transform: translateY(-2px);
        }

        /* Tableau */
        .res-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(20, 30, 50, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(6, 182, 212, 0.15);
            border-radius: 12px;
            overflow: hidden;
        }

        .res-table th,
        .res-table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid rgba(6, 182, 212, 0.05);
        }

        .res-table th {
            background: rgba(6, 182, 212, 0.08);
            font-weight: 700;
            color: #06b6d4;
            text-transform: uppercase;
            font-size: 0.85em;
        }

        .res-table tr:last-child td {
            border-bottom: none;
        }

        .res-table tbody tr:hover {
            background: rgba(6, 182, 212, 0.05);
        }

        .res-table td {
            color: #a0aec0;
        }

        .res-table strong {
            color: #ffffff;
        }

        .res-table small {
            color: #6b7a90;
            display: block;
            margin-top: 4px;
        }

        /* Bouton Action */
        .btn-action {
            display: inline-block;
            padding: 8px 16px;
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85em;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-action:hover {
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
            transform: translateY(-2px);
        }

        /* Badges de Statut */
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: capitalize;
            display: inline-block;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-approved {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-refused {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .status-finished {
            background: rgba(107, 122, 144, 0.15);
            color: #cbd5e1;
            border: 1px solid rgba(107, 122, 144, 0.3);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 40px;
            background: rgba(20, 30, 50, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.15);
            border-radius: 12px;
            backdrop-filter: blur(20px);
            color: #6b7a90;
        }

        .empty-state p {
            font-size: 1.1em;
            margin: 0;
        }
    </style>

    <div class="container">

        <div class="header-box">
            <h1>Mes Réservations</h1>
            <a href="{{ route('resources.index') }}" class="btn-new">Faire une nouvelle reservation</a>
        </div>

        <div class="filter-bar">
            <form action="{{ route('internal.reservations') }}" method="GET" class="search-form">

                <input type="text" name="search" class="input-dark input-search"
                    placeholder="Rechercher une ressource (ex: Serveur Dell)" value="{{ request('search') }}">

                <select name="status" class="input-dark input-select" onchange="this.form.submit()">
                    <option value="all">Tous les statuts</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvée</option>
                    <option value="refused" {{ request('status') == 'refused' ? 'selected' : '' }}>Refusée</option>
                    <option value="finished" {{ request('status') == 'finished' ? 'selected' : '' }}>Terminée</option>
                </select>

                <button type="submit" class="btn-filter">Filtrer</button>

                @if(request()->filled('search') || (request()->filled('status') && request('status') != 'all'))
                    <a href="{{ route('internal.reservations') }}" class="btn-reset">Réinitialiser</a>
                @endif
            </form>
        </div>

        @if($reservations->isEmpty())
            <div class="empty-state">
                <p>Aucune réservation trouvée pour ces critères.</p>
            </div>
        @else
            <table class="res-table">
                <thead>
                    <tr>
                        <th>Ressource</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Justification</th>
                        <th>Statut</th>
                        <th>Date demande</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservations as $res)
                        <tr>
                            <td>
                                <strong>{{ $res->resource->name }}</strong><br>
                                <small>{{ $res->resource->category->name ?? 'Matériel' }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($res->start_date)->format('d/m/Y H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($res->end_date)->format('d/m/Y H:i') }}</td>
                            <td>{{ Str::limit($res->justification, 50) }}</td>
                            <td>
                                <span class="badge status-{{ $res->status }}">
                                    @if($res->status == 'pending') En attente
                                    @elseif($res->status == 'approved') Approuvée
                                    @elseif($res->status == 'refused') Refusée
                                    @else {{ $res->status }}
                                    @endif
                                </span>
                            </td>
                            <td>{{ $res->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($res->status == 'approved')
                                    <a href="{{ route('internal.incidents.report', ['resource_id' => $res->resource_id]) }}"
                                        class="btn-action">
                                        Signaler Incident
                                    </a>
                                @else
                                    <span style="color: #6b7a90; font-size: 0.85em;">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection