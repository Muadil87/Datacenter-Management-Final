@extends('layout')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>Gestion du Parc Informatique</h2>
    <button class="success">+ Ajouter une ressource</button>
</div>

<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr style="background: #ecf0f1; text-align: left;">
            <th style="padding: 10px; border-bottom: 2px solid #bdc3c7;">Nom</th>
            <th style="padding: 10px; border-bottom: 2px solid #bdc3c7;">Catégorie</th>
            <th style="padding: 10px; border-bottom: 2px solid #bdc3c7;">Description</th>
            <th style="padding: 10px; border-bottom: 2px solid #bdc3c7;">État Actuel</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resources as $resource)
        <tr style="border-bottom: 1px solid #ecf0f1;">
            <td style="padding: 10px; font-weight: bold;">{{ $resource->name }}</td>
            <td style="padding: 10px;">{{ $resource->category }}</td>
            <td style="padding: 10px; color: #7f8c8d; font-size: 0.9em;">{{ $resource->description }}</td>
            <td style="padding: 10px;">
                @if($resource->status == 'available')
                    <span style="background: #d4edda; color: #155724; padding: 5px 10px; border-radius: 15px; font-size: 0.85em; font-weight: bold;">Disponible</span>
                @elseif($resource->status == 'occupied')
                    <span style="background: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 15px; font-size: 0.85em; font-weight: bold;">Occupé</span>
                @else
                    <span style="background: #f8d7da; color: #721c24; padding: 5px 10px; border-radius: 15px; font-size: 0.85em; font-weight: bold;">Maintenance</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection