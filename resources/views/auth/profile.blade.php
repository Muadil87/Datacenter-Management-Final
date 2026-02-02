@extends('layout')

@section('content')
    <style>
        :root {
            --bg-dark: #0f1419;
            --bg-card: rgba(20, 30, 50, 0.6);
            --border-color: rgba(6, 182, 212, 0.15);
            --text-primary: #ffffff;
            --text-secondary: #a0aec0;
            --text-tertiary: #6b7a90;
            --accent: #06b6d4;
            --accent-light: #22d3ee;
        }

        body {
            background: var(--bg-dark);
            color: var(--text-primary);
        }

        .profile-container {
            max-width: none;
            width: 100%;
            margin: auto;
            padding: 40px 20px;
        }

        .profile-title {
            text-align: center;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 30px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-left-color: #ef4444;
            color: #fca5a5;
        }

        .alert-error p {
            color: #fca5a5;
            margin: 5px 0;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-left-color: #10b981;
            color: #86efac;
        }

        .profile-card {
            background: var(--bg-card);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(20px);
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section-title {
            font-size: 1.3em;
            font-weight: 700;
            padding-bottom: 12px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--accent);
            color: var(--accent);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: rgba(15, 20, 25, 0.5);
            color: var(--text-primary);
            font-size: 1em;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.2);
        }

        .form-input::placeholder {
            color: var(--text-tertiary);
        }

        .profile-photo-section {
            text-align: center;
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 0;
            border: 3px solid var(--accent);
        }
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            font-weight: bold;
            margin: 0 auto 20px;
            border: 3px solid var(--accent);
        }

        .photo-label {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent) 0%, #0891b2 100%);
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
        }

        .photo-label:hover {
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
            transform: translateY(-2px);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .checkbox-input {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--accent);
        }

        .checkbox-label {
            margin: 0;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .form-divider {
            margin: 25px 0;
            border: none;
            border-top: 1px solid var(--border-color);
        }

        .submit-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--accent) 0%, #0891b2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1em;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .submit-button:hover {
            box-shadow: 0 8px 20px rgba(6, 182, 212, 0.3);
            transform: translateY(-2px);
        }

        .submit-button:active {
            transform: translateY(0);
        }

        .help-text {
            font-size: 0.85em;
            color: var(--text-tertiary);
            margin-bottom: 15px;
        }
    </style>

    <div class="profile-container">
        <h1 class="profile-title">Mon Profil</h1>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="profile-card">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <!-- Profile Photo Section -->
                <div class="form-section">
                    <div class="profile-photo-section">
                        <div id="avatar" class="profile-avatar"
                            style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: bold; border: 3px solid var(--accent); margin-bottom: 5px;">
                            @if($user->profile_photo)
                                <img id="preview" src="/storage/{{ $user->profile_photo }}" alt="Profile" class="profile-photo"
                                    style="margin: 0;">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                            @endif
                        </div>
                        <label for="profile_photo" class="photo-label">
                            <i class="fas fa-camera"></i> Changer la photo
                        </label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display: none;">
                        <script>
                            document.getElementById('profile_photo').addEventListener('change', function (e) {
                                const file = e.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = function (event) {
                                        let avatar = document.getElementById('avatar');
                                        let preview = document.getElementById('preview');

                                        // Clear the avatar content and add the preview image
                                        avatar.innerHTML = '';

                                        if (!preview) {
                                            preview = document.createElement('img');
                                            preview.id = 'preview';
                                            preview.className = 'profile-photo';
                                            preview.style.margin = '0';
                                        }

                                        preview.src = event.target.result;
                                        preview.alt = 'Profile Preview';
                                        avatar.appendChild(preview);
                                    };
                                    reader.readAsDataURL(file);
                                }
                            });
                        </script>
                    </div>
                </div>

                <!-- Personal Information -->
                <!-- Personal Info Section -->
                <div class="form-section">
                    <h3 class="form-section-title">Informations Personnelles</h3>

                    <div class="form-group">
                        <label class="form-label">Nom complet :</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
                    </div>

                    <!-- Email - Only for users who registered directly (not via OAuth) -->
                    @if(empty($user->auth_provider))
                        <div class="form-group">
                            <label class="form-label">Email :</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="form-input">
                        </div>
                    @endif
                </div>

                <hr class="form-divider">

                <!-- Password Section - Only for users who registered directly (not via OAuth) -->
                @if(empty($user->auth_provider))
                    <div class="form-section">
                        <h3 class="form-section-title">Changer le Mot de Passe</h3>
                        <p class="help-text">Laissez vide pour garder votre mot de passe actuel</p>

                        <div class="form-group">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="password" placeholder="(Optionnel)" class="form-input">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation" placeholder="(Optionnel)" class="form-input">
                        </div>
                    </div>

                    <hr class="form-divider">
                @endif

                <!-- Notification Preferences -->
                <div class="form-section">
                    <h3 class="form-section-title">Préférences de Notification</h3>

                    <div class="checkbox-group">
                        <input type="checkbox" id="notification_email" name="notification_email" value="1"
                            @if($user->notification_email) checked @endif class="checkbox-input">
                        <label for="notification_email" class="checkbox-label">Recevoir les notifications par email</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="notification_incidents" name="notification_incidents" value="1"
                            @if($user->notification_incidents) checked @endif class="checkbox-input">
                        <label for="notification_incidents" class="checkbox-label">Recevoir les notifications sur les
                            incidents</label>
                    </div>
                </div>

                <button type="submit" class="submit-button">Mettre à jour le profil</button>
            </form>
        </div>
    </div>
@endsection