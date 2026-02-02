@extends('layouts.app')

@section('content')
    <div class="reservation-container">
        <h2 class="reservation-title">Réserver la ressource : {{ $resource->name }}</h2>

        <div class="resource-details-card">
            <p><strong>Type :</strong> {{ $resource->category->name ?? 'Autre' }}</p>
            <p><strong>Description :</strong> {{ $resource->description }}</p>
        </div>

        <!-- Availability Status -->
        <div id="availabilityStatus" class="availability-status"></div>

        @if ($errors->any())
            <div class="alert-error">
                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('reservations.store', $resource->id) }}" method="POST" class="reservation-form">
            @csrf

            <div class="form-group">
                <label>Date de début :</label>
                <input type="datetime-local" name="start_date" required>
            </div>

            <div class="form-group">
                <label>Date de fin :</label>
                <input type="datetime-local" name="end_date" required>
            </div>

            <div class="form-group">
                <label>Justification (Pourquoi avez-vous besoin de cette ressource ?) :</label>
                <textarea name="justification" rows="4" required></textarea>
            </div>

            <button type="submit" class="btn-submit">Envoyer la demande</button>
        </form>
    </div>

    <style>
        .reservation-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .reservation-title {
            color: #ffffff;
            font-size: 1.8rem;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .resource-details-card {
            background: rgba(20, 30, 50, 0.6);
            border: 1px solid rgba(6, 182, 212, 0.2);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            backdrop-filter: blur(10px);
        }

        .resource-details-card p {
            color: #cbd5e1;
            margin: 10px 0;
            font-size: 0.95rem;
        }

        .resource-details-card strong {
            color: #06b6d4;
        }

        .availability-status {
            background: rgba(6, 182, 212, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #06b6d4;
            backdrop-filter: blur(10px);
        }

        .availability-status strong {
            display: block;
            margin-bottom: 12px;
            color: #06b6d4;
            font-size: 0.95rem;
        }

        .period-item {
            background: rgba(15, 23, 42, 0.8);
            padding: 12px;
            margin-bottom: 8px;
            border-left: 4px solid #ef4444;
            border-radius: 4px;
            color: #cbd5e1;
            font-size: 0.9rem;
        }

        .period-item.free {
            border-left-color: #10b981;
        }

        .period-item i {
            margin-right: 6px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #cbd5e1;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 6px;
            background: rgba(15, 23, 42, 0.8);
            color: #ffffff;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #06b6d4;
            background: rgba(15, 23, 42, 0.95);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            cursor: pointer;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s;
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 25px rgba(6, 182, 212, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert-error {
            color: #fca5a5;
            background: rgba(239, 68, 68, 0.1);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            border-left: 4px solid #ef4444;
            font-size: 0.9rem;
        }

        .alert-error ul {
            margin: 0;
            padding-left: 20px;
        }

        .alert-error li {
            margin-bottom: 5px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const resourceId = {{ $resource->id }};

            fetch(`/api/resource/${resourceId}/reservations`)
                .then(res => res.json())
                .then(data => {
                    const statusDiv = document.getElementById('availabilityStatus');
                    let html = '<strong><i class="fas fa-calendar"></i> Période d\'occupation et disponibilité :</strong>';

                    if (data.reservations && data.reservations.length > 0) {
                        data.reservations.forEach(res => {
                            const startDate = new Date(res.start_date).toLocaleDateString('fr-FR');
                            const endDate = new Date(res.end_date).toLocaleDateString('fr-FR');
                            html += `
                                    <div class="period-item">
                                        <i class="fas fa-circle" style="color: #ef4444;"></i> Occupé : ${startDate} → ${endDate}
                                    </div>
                                `;
                        });
                        html += '<div class="period-item free"><i class="fas fa-check-circle"></i> Libre en dehors de ces périodes</div>';
                    } else {
                        html += '<div class="period-item free"><i class="fas fa-check-circle"></i> Ressource entièrement disponible</div>';
                    }

                    statusDiv.innerHTML = html;
                })
                .catch(err => {
                    console.error('Erreur:', err);
                    document.getElementById('availabilityStatus').innerHTML = '<strong>Vérification de disponibilité...</strong>';
                });
        });
    </script>
@endsection