<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Signaler un Incident</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
</head>
<body>

    <div class="main-content" style="max-width: 600px; margin: 50px auto; padding: 20px;">
        <h1 class="page-title">Signaler un nouvel Incident</h1>

        @if(session('success'))
            <div style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <form action="{{ route('incidents.store') }}" method="POST" id="incidentForm">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label for="resource_id" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Ressource concernée</label>
                    <select id="resource_id" name="resource_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em;">
                        <option value="">-- Sélectionner une ressource --</option>
                        @forelse($resources ?? [] as $resource)
                            <option value="{{ $resource->id }}" {{ request('resource_id') == $resource->id ? 'selected' : '' }}>{{ $resource->name }}</option>
                        @empty
                            <option value="">Aucune ressource disponible</option>
                        @endforelse
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="title" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Titre du problème</label>
                    <input type="text" id="title" name="title" placeholder="Ex: Serveur inaccessible" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Description du problème</label>
                    <textarea id="description" name="description" rows="5" placeholder="Décrivez le problème en détails..." required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; font-family: inherit;"></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" id="submitBtn" style="flex: 1; padding: 12px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 1em;">
                        Envoyer le signalement
                    </button>
                    <a href="{{ route('home') }}" style="flex: 1; padding: 12px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 1em; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center;">
                        ← Retour
                    </a>
                </div>
            </form>
        </div>

        <script>
            document.getElementById('incidentForm').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                
                // Prevent double submission
                if (submitBtn.disabled) {
                    e.preventDefault();
                    return false;
                }
                
                // Disable button and show loading state
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.6';
                submitBtn.style.cursor = 'not-allowed';
                submitBtn.textContent = 'Envoi en cours...';
            });
        </script>
    </div>

    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>