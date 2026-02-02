@extends('layouts.app')

@section('content')
    <style>
        .user-card {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 20px;
            align-items: center;
        }

        .user-info h3 {
            color: #ffffff;
            margin: 0 0 5px 0;
            font-size: 1.1rem;
        }

        .user-info p {
            color: #94a3b8;
            margin: 5px 0;
            font-size: 0.95rem;
        }

        .user-column {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .user-column h3 {
            color: #ffffff;
            margin: 0;
            font-size: 1rem;
        }

        .user-column-title {
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-right: 10px;
        }

        .status-pending {
            background: rgba(249, 115, 22, 0.2);
            color: #fb923c;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.2);
            color: #86efac;
        }

        .role-selector {
            background: #1e293b;
            border: 1px solid #334155;
            color: #e2e8f0;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .role-selector:hover {
            border-color: #64748b;
            background: #0f172a;
        }

        .role-selector:focus {
            outline: none;
            border-color: #06b6d4;
            background: #0f172a;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-approve {
            background: #22c55e;
            color: white;
        }

        .btn-approve:hover {
            background: #16a34a;
        }

        .btn-reject {
            background: #ef4444;
            color: white;
        }

        .btn-reject:hover {
            background: #dc2626;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #334155;
            padding-bottom: 15px;
        }

        .filter-tab {
            padding: 8px 16px;
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-weight: 600;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }

        .filter-tab.active {
            color: #06b6d4;
            border-bottom-color: #06b6d4;
        }

        .filter-tab:hover {
            color: #cbd5e1;
        }
    </style>

    <header style="margin-bottom: 40px;">
        <h1 style="color: #ffffff; margin-bottom: 10px;">Gestion des Utilisateurs</h1>
        <p style="color: #94a3b8;">Vérifier et gérer les comptes utilisateurs</p>
    </header>

    <div class="filter-tabs">
        <button class="filter-tab active" onclick="filterUsers('all')">Tous les utilisateurs ({{ $allUsers->count() }})</button>
        <button class="filter-tab" onclick="filterUsers('pending')">En attente d'approbation ({{ $pendingUsers->count() }})</button>
        <button class="filter-tab" onclick="filterUsers('approved')">Approuvés ({{ $approvedUsers->count() }})</button>
    </div>

    <div id="pending-section">
        <h2 style="color: #cbd5e1; margin-top: 30px; margin-bottom: 20px;">
            <i class="fas fa-hourglass-half"></i> Vérification en attente ({{ $pendingUsers->count() }})
        </h2>

        @if($pendingUsers->isEmpty())
            <div
                style="text-align: center; padding: 40px; background: #0f172a; border-radius: 8px; border: 1px dashed #334155;">
                <p style="color: #94a3b8; font-size: 1.05rem;">✓ Aucun utilisateur en attente</p>
            </div>
        @else
            @foreach($pendingUsers as $user)
                <div class="user-card">
                    <div class="user-info">
                        <h3>{{ $user->name }}</h3>
                        <p><i class="fas fa-envelope"></i> {{ $user->email }}</p>
                        <p><i class="fas fa-clock"></i> Inscrit le : {{ $user->created_at->format('d/m/Y H:i') }}</p>
                        <span class="status-badge status-pending">
                            <i class="fas fa-hourglass-half"></i> En attente
                        </span>
                    </div>
                    <div class="action-buttons">
                        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-approve" title="Approuver l'utilisateur comme Interne">
                                <i class="fas fa-check"></i> Approuver (Interne)
                            </button>
                        </form>
                        <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" style="display:inline;"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir rejeter cet utilisateur ?');">
                            @csrf
                            <button type="submit" class="btn btn-reject" title="Rejeter l'utilisateur">
                                <i class="fas fa-times"></i> Rejeter
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div id="approved-section" style="display:none;">
        <h2 style="color: #cbd5e1; margin-top: 30px; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> Utilisateurs approuvés ({{ $approvedUsers->count() }})
        </h2>

        @if($approvedUsers->isEmpty())
            <div
                style="text-align: center; padding: 40px; background: #0f172a; border-radius: 8px; border: 1px dashed #334155;">
                <p style="color: #94a3b8; font-size: 1.05rem;">Aucun utilisateur approuvé pour l'instant</p>
            </div>
        @else
            @foreach($approvedUsers as $user)
                <div class="user-card">
                    <div class="user-info">
                        <h3>{{ $user->name }}</h3>
                        <p><i class="fas fa-envelope"></i> {{ $user->email }}</p>
                        <p><i class="fas fa-user-tag"></i> Rôle : <strong>{{ ucfirst($user->role) }}</strong></p>
                        <p><i class="fas fa-calendar"></i> Approuvé le : {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                        <span class="status-badge status-active">
                            <i class="fas fa-check-circle"></i> Actif
                        </span>
                    </div>
                    <div class="action-buttons">
                        <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST" style="display:inline;"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur ?');">
                            @csrf
                            <button type="submit" class="btn btn-reject" title="Désactiver l'utilisateur">
                                <i class="fas fa-ban"></i> Désactiver
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div id="all-section" style="display:none;">
        <h2 style="color: #cbd5e1; margin-top: 30px; margin-bottom: 20px;">
            <i class="fas fa-users"></i> Tous les utilisateurs ({{ $allUsers->count() }})
        </h2>

        @foreach($allUsers as $user)
            <div class="user-card">
                <div class="user-column">
                    <div class="user-column-title">Nom & Email</div>
                    <h3>{{ $user->name }}</h3>
                    <p style="font-size: 0.9rem; color: #94a3b8;"><i class="fas fa-envelope"></i> {{ $user->email }}</p>
                </div>

                <div class="user-column">
                    <div class="user-column-title">Statut</div>
                    @if($user->is_active)
                        <span class="status-badge status-active">
                            <i class="fas fa-check-circle"></i> Actif
                        </span>
                    @else
                        <span class="status-badge status-pending">
                            <i class="fas fa-hourglass-half"></i> En attente
                        </span>
                    @endif
                    <p style="font-size: 0.9rem; color: #94a3b8;"><i class="fas fa-calendar"></i>
                        {{ $user->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="user-column">
                    <div class="user-column-title">Rôle</div>
                    <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <select name="role" class="role-selector" onchange="this.form.submit();">
                            <option value="internal" {{ $user->role === 'internal' ? 'selected' : '' }}>Interne</option>
                            <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Gestionnaire</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </form>
                </div>

                <div class="action-buttons">
                    <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST" style="display:inline;"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur ?');">
                        @csrf
                        <button type="submit" class="btn btn-reject" title="Désactiver l'utilisateur">
                            <i class="fas fa-ban"></i> Désactiver
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function filterUsers(filter) {
            // Hide all sections
            document.getElementById('pending-section').style.display = 'none';
            document.getElementById('approved-section').style.display = 'none';
            document.getElementById('all-section').style.display = 'none';

            // Remove active class from all tabs
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.classList.remove('active');
            });

            // Show selected section and mark tab as active
            if (filter === 'pending') {
                document.getElementById('pending-section').style.display = 'block';
                event.target.classList.add('active');
            } else if (filter === 'approved') {
                document.getElementById('approved-section').style.display = 'block';
                event.target.classList.add('active');
            } else {
                document.getElementById('all-section').style.display = 'block';
                document.querySelectorAll('.filter-tab')[0].classList.add('active');
            }
        }
    </script>

@endsection