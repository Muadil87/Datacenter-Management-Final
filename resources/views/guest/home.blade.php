@extends('layout')

@section('content')

    <div style="margin-bottom: 30px;">
        <h1 style="color: #2c3e50; margin-bottom: 10px;">Bienvenue au DataCenter</h1>
        <p style="color: #7f8c8d; font-size: 1.1em;">
            @guest
                Vous êtes en tant que <strong>Visiteur</strong>
            @else
                Connecté en tant que <strong>{{ ucfirst(Auth::user()->role) }}</strong>
            @endguest
        </p>
    </div>

    @guest
        <div
            style="background: #e8f6f3; padding: 20px; border-left: 5px solid #2ecc71; margin-bottom: 30px; border-radius: 5px;">
            <h3 style="color: #27ae60; margin-top: 0;">Vous n'avez pas de compte ?</h3>
            <p style="color: #555;">Pour réserver des ressources, vous devez créer un compte et être approuvé par un
                administrateur.</p>

            <a href="{{ route('register') }}"
                style="display: inline-block; background: #2ecc71; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;">
                <i class="fas fa-user-plus"></i> Créer un compte
            </a>
            <a href="{{ route('login') }}"
                style="display: inline-block; background: #3498db; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                <i class="fas fa-lock"></i> Se connecter
            </a>
        </div>
    @endguest

    <div style="margin-top: 30px;">
        <h2 style="color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px;"><i class="fas fa-boxes"></i> Catalogue des Ressources</h2>

        @if($resources->isEmpty())
            <div style="padding: 30px; background: #f8f9fa; border-radius: 5px; text-align: center;">
                <p style="color: #7f8c8d; font-size: 1.1em;">Aucune ressource disponible pour le moment.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <thead>
                        <tr style="background: #34495e; color: white;">
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Nom</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Catégorie</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Description</th>
                            <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">État</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resources as $resource)
                            <tr style="background: #fff; border: 1px solid #ddd;">
                                <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">{{ $resource->name }}</td>
                                <td style="padding: 12px; border: 1px solid #ddd;">{{ $resource->category ?? 'N/A' }}</td>
                                <td style="padding: 12px; border: 1px solid #ddd;">{{ Str::limit($resource->description, 50) }}</td>
                                <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">
                                    @if($resource->status == 'available')
                                        <span
                                            style="background: #2ecc71; color: white; padding: 5px 10px; border-radius: 3px; font-weight: bold;">✓
                                            Disponible</span>
                                    @elseif($resource->status == 'occupied')
                                        <span
                                            style="background: #f39c12; color: white; padding: 5px 10px; border-radius: 3px; font-weight: bold;">⏱
                                            Occupé</span>
                                    @else
                                        <span
                                            style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 3px; font-weight: bold;"><i class="fas fa-wrench"></i>
                                            Maintenance</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection