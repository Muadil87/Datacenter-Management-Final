@extends('layout')

@section('content')
<h2>Espace Utilisateur</h2>
<p>Bienvenue, {{ Auth::user()->name }}. Vous pouvez ici consulter et réserver des ressources.</p>

<div style="margin-top: 20px;">
    <h3>Mes Réservations en cours</h3>
    <div class="alert" style="background: #eef; color: #333;">
        <em>Aucune réservation active (Fonctionnalité à venir).</em>
    </div>
</div>

<div style="margin-top: 30px;">
    <h3>Ressources Disponibles pour Réservation</h3>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>État</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resources as $resource)
            <tr>
                <td>{{ $resource->name }}</td>
                <td>{{ $resource->description }}</td>
                <td>
                    @if($resource->status == 'available')
                        <span style="color: green">Disponible</span>
                    @else
                        <span style="color: orange">Occupé</span>
                    @endif
                </td>
                <td>
                    @if($resource->status == 'available')
                        <button class="success">Réserver</button> @else
                        <button disabled style="opacity: 0.5; cursor: not-allowed;">Indisponible</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection