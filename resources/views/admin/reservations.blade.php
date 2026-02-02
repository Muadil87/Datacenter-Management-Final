@extends('layout')

@section('content')
<div class="admin-reservations">
    <h1 style="color: #2c3e50; margin-bottom: 30px;">Gestion Globale des Réservations</h1>
    
    @if(session('success'))
        <div class="alert-success" style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="stats-cards" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card" style="background: #fff3cd; padding: 20px; border-radius: 8px; text-align: center;">
            <div style="font-size: 2em; color: #ff9800; font-weight: bold;">●</div>
            <div style="font-size: 2em; font-weight: bold; color: #ff9800;">{{ $pendingCount }}</div>
            <div style="color: #666;">En attente</div>
        </div>
        
        <div class="stat-card" style="background: #d4edda; padding: 20px; border-radius: 8px; text-align: center;">
            <div style="font-size: 2em; color: #28a745; font-weight: bold;">✓</div>
            <div style="font-size: 2em; font-weight: bold; color: #28a745;">{{ $approvedCount }}</div>
            <div style="color: #666;">Approuvées</div>
        </div>
        
        <div class="stat-card" style="background: #f8d7da; padding: 20px; border-radius: 8px; text-align: center;">
            <div style="font-size: 2em; color: #dc3545; font-weight: bold;">✕</div>
            <div style="font-size: 2em; font-weight: bold; color: #dc3545;">{{ $refusedCount }}</div>
            <div style="color: #666;">Refusées</div>
        </div>
    </div>

    <!-- All Reservations Table -->
    <div class="section-box" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #2c3e50; margin-bottom: 20px;">Toutes les Réservations</h2>

        @if(count($allReservations) > 0)
            <div style="overflow-x: auto;">
                <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <th style="padding: 15px; text-align: left; font-weight: 600;"><i class="fas fa-user"></i> Utilisateur</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600;">Ressource</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600;"><i class="fas fa-calendar"></i> Début</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600;"><i class="fas fa-calendar"></i> Fin</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600;">Justification</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600;">Statut</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600;"><i class="fas fa-calendar"></i> Crée</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allReservations as $reservation)
                        <tr style="border-bottom: 1px solid #ecf0f1;">
                            <td style="padding: 15px;"><strong>{{ $reservation->user->name }}</strong></td>
                            <td style="padding: 15px;">{{ $reservation->resource->name }}</td>
                            <td style="padding: 15px;">{{ \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y H:i') }}</td>
                            <td style="padding: 15px;">{{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y H:i') }}</td>
                            <td style="padding: 15px; max-width: 250px; word-wrap: break-word;">
                                <small>{{ substr($reservation->justification, 0, 50) }}{{ strlen($reservation->justification) > 50 ? '...' : '' }}</small>
                            </td>
                            <td style="padding: 15px;">
                                @if($reservation->status === 'pending')
                                    <span style="background: #fff3cd; color: #ff9800; padding: 5px 10px; border-radius: 4px; font-weight: 600;">En attente</span>
                                @elseif($reservation->status === 'approved')
                                    <span style="background: #d4edda; color: #28a745; padding: 5px 10px; border-radius: 4px; font-weight: 600;">Approuvée</span>
                                @elseif($reservation->status === 'refused')
                                    <span style="background: #f8d7da; color: #dc3545; padding: 5px 10px; border-radius: 4px; font-weight: 600;">Refusée</span>
                                @endif
                            </td>
                            <td style="padding: 15px; font-size: 0.9em; color: #7f8c8d;">
                                {{ $reservation->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="background: #f9f9f9; padding: 30px; text-align: center; border-radius: 4px; color: #7f8c8d;">
                <p style="font-size: 1.1em;">Aucune réservation pour le moment</p>
            </div>
        @endif
    </div>

    <!-- Legend -->
    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-top: 30px;">
        <h3 style="margin-top: 0;">Légende des Statuts</h3>
        <ul style="margin: 0; padding-left: 20px; color: #555;">
            <li><strong>En attente:</strong> Demande en cours, en attente d'approbation du gestionnaire</li>
            <li><strong>Approuvée:</strong> Demande approuvée par le gestionnaire, réservation confirmée</li>
            <li><strong>Refusée:</strong> Demande refusée par le gestionnaire</li>
        </ul>
    </div>
</div>

<style>
    .admin-reservations {
        padding: 30px;
        max-width: 1600px;
        margin: 0 auto;
        background: white;
    }

    .admin-table tbody tr:hover {
        background: #f8f9fa;
    }
</style>
@endsection
