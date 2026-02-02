@extends('layouts.app')

@section('content')

    <style>
        /* Force Dark Mode */
        body {
            background-color: #0a0e17 !important;
            color: #ffffff !important;
            font-family: 'Inter', sans-serif;
        }

        .home-wrapper {
            position: relative;
            overflow: hidden;
        }

        /* Background Orbs */
        .bg-orbs {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.3;
        }

        .orb-1 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, #06b6d4 0%, transparent 70%);
            top: -200px;
            left: -200px;
        }

        .orb-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #3b82f6 0%, transparent 70%);
            bottom: 0;
            right: -100px;
        }

        .orb-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, #8b5cf6 0%, transparent 70%);
            top: 40%;
            right: 20%;
            opacity: 0.15;
        }

        /* --- HERO SECTION --- */
        .hero-section {
            position: relative;
            min-height: 85vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px;
            z-index: 1;
        }

        .hero-bg-image {
            position: absolute;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1558494949-ef526b0042a0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            opacity: 0.15;
            z-index: -1;
            mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
            -webkit-mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
        }

        .hero-content h1 {
            font-size: clamp(3rem, 6vw, 5rem);
            font-weight: 900;
            margin-bottom: 25px;
            line-height: 1.1;
            text-shadow: 0 0 30px rgba(0, 0, 0, 0.8);
        }

        .hero-content p {
            font-size: 1.3rem;
            color: #cbd5e0;
            max-width: 750px;
            margin: 0 auto 50px auto;
            line-height: 1.6;
        }

        .btn-hero {
            padding: 18px 40px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
        }

        .btn-primary-glow {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%);
            color: white;
            box-shadow: 0 0 25px rgba(6, 182, 212, 0.5);
            border: 1px solid transparent;
        }

        .btn-primary-glow:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 50px rgba(6, 182, 212, 0.5);
        }

        .btn-outline-glow {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            backdrop-filter: blur(5px);
        }

        .btn-outline-glow:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #06b6d4;
        }

        /* --- STATS BAR --- */
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 50px;
            flex-wrap: wrap;
            padding: 40px 20px;
            background: rgba(0, 0, 0, 0.3);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: -50px;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(10px);
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #06b6d4;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #cbd5e0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* --- COMMON SECTIONS --- */
        .section-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 20px;
            position: relative;
            z-index: 1;
        }

        .section-title {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(90deg, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: #cbd5e0;
            margin-bottom: 60px;
            max-width: 600px;
        }

        .center-text {
            text-align: center;
            margin-left: auto;
            margin-right: auto;
        }

        /* --- CARDS & GRIDS --- */
        .glass-card {
            background: rgba(20, 30, 50, 0.7);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 35px;
            transition: transform 0.3s, border-color 0.3s, background 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .glass-card:hover {
            transform: translateY(-8px);
            border-color: #06b6d4;
            background: rgba(30, 40, 70, 0.8);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: inline-block;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        /* --- STEPS (HOW IT WORKS) --- */
        .steps-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .step-item {
            position: relative;
            padding: 20px;
            border-left: 2px solid rgba(255, 255, 255, 0.1);
        }

        .step-number {
            font-size: 4rem;
            font-weight: 900;
            opacity: 0.1;
            position: absolute;
            top: -10px;
            left: 10px;
            color: #06b6d4;
        }

        .step-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
        }

        .step-desc {
            font-size: 0.95rem;
            color: #cbd5e0;
            line-height: 1.5;
        }

        /* --- SPLIT LAYOUT --- */
        .split-layout {
            display: flex;
            align-items: center;
            gap: 60px;
            flex-wrap: wrap;
        }

        .text-block {
            flex: 1;
            min-width: 300px;
        }

        .image-block {
            flex: 1;
            min-width: 300px;
        }

        .image-card {
            width: 100%;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.5s;
        }

        .image-card:hover {
            transform: scale(1.02) rotate(1deg);
        }

        /* --- TECH STACK --- */
        .tech-stack {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            opacity: 0.7;
            margin-top: 30px;
        }

        .tech-item {
            text-align: center;
        }

        .tech-item span {
            display: block;
            font-weight: 600;
            margin-top: 10px;
            font-size: 0.9rem;
        }

        /* --- FAQ ACCORDION --- */
        .faq-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
            border-radius: 12px;
            overflow: hidden;
        }

        .faq-question {
            padding: 20px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-question:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .faq-answer {
            padding: 0 20px 20px 20px;
            color: #cbd5e0;
            font-size: 0.95rem;
            line-height: 1.6;
            display: none;
            /* JS needed for real toggle, using CSS hover for demo simplicity or keep simple */
        }

        .faq-item:hover .faq-answer {
            display: block;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- CTA FOOTER --- */
        .cta-section {
            background: linear-gradient(to right, rgba(6, 182, 212, 0.1), transparent);
            text-align: center;
            padding: 80px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>

    <div class="home-wrapper">
        <div class="bg-orbs">
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>
        </div>

        <div class="hero-section">
            <div class="hero-bg-image"></div>

            <div class="hero-content">
                <span
                    style="color: #06b6d4; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 15px; display: block;">Infrastructure
                    Management System</span>
                <h1>Gérez votre Data Center<br><span style="color: #06b6d4;">Sans Compromis</span></h1>
                <p>
                    La solution ultime pour centraliser, réserver et superviser vos ressources informatiques.
                    Optimisez l'allocation de vos serveurs, VMs et équipements réseaux en temps réel.
                </p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    @guest
                        <a href="{{ route('login') }}" class="btn-hero btn-primary-glow">
                            Commencer maintenant <span>→</span>
                        </a>
                        <a href="{{ route('resources.index') }}" class="btn-hero btn-outline-glow">
                            Explorer le catalogue
                        </a>
                        <a href="{{ route('resources.index') }}" class="btn-hero btn-outline-glow">
                            Accéder au catalogue <span>→</span>
                        </a>
                    @else
                        <a href="{{ route('resources.index') }}" class="btn-hero btn-primary-glow">
                            Accéder au catalogue <span>→</span>
                        </a>
                    @endguest
                </div>
            </div>
        </div>

        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-number">99.9%</span>
                <span class="stat-label">Disponibilité</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">24/7</span>
                <span class="stat-label">Surveillance</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">100+</span>
                <span class="stat-label">Ressources</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">0s</span>
                <span class="stat-label">Latence Réservation</span>
            </div>
        </div>

        <div class="section-container">
            <div class="split-layout">
                <div class="text-block">
                    <h2 class="section-title">Centralisation & Performance</h2>
                    <p style="color: #cbd5e0; line-height: 1.8; margin-bottom: 30px; font-size: 1.1rem;">
                        Dans un environnement complexe, la gestion manuelle via Excel ou emails est obsolète.
                        Notre application unifie la gestion de votre infrastructure.
                    </p>

                    <div style="display: grid; gap: 20px;">
                        <div style="display: flex; gap: 15px;">
                            <div
                                style="background: rgba(6,182,212,0.1); padding: 10px; border-radius: 10px; height: fit-content;">
                                🚀</div>
                            <div>
                                <h4 style="margin-bottom: 5px;">Base de Données Unifiée</h4>
                                <p style="font-size: 0.9rem; color: #cbd5e0;">Tous vos équipements (Serveurs,
                                    Stockage, Réseau) au même endroit.</p>
                            </div>
                        </div>
                        <div style="display: flex; gap: 15px;">
                            <div
                                style="background: rgba(6,182,212,0.1); padding: 10px; border-radius: 10px; height: fit-content;">
                                ⚡</div>
                            <div>
                                <h4 style="margin-bottom: 5px;">Réservation Intelligente</h4>
                                <p style="font-size: 0.9rem; color: #cbd5e0;">Détection automatique des
                                    conflits de dates (Overlapping).</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="image-block">
                    <img src="https://plus.unsplash.com/premium_photo-1661386253258-64ab9521ce89?q=80&w=870&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                        alt="Server Room" class="image-card">
                </div>
            </div>
        </div>

        <div class="section-container" style="background: rgba(255,255,255,0.02); border-radius: 30px;">
            <div class="center-text">
                <h2 class="section-title">Comment ça marche ?</h2>
                <p class="section-subtitle">Réserver une ressource n'a jamais été aussi simple et transparent.</p>
            </div>

            <div class="steps-container">
                <div class="step-item">
                    <span class="step-number">01</span>
                    <h3 class="step-title">Inscription</h3>
                    <p class="step-desc">Créez votre compte interne ou invité. Votre accès sera validé par un
                        administrateur.</p>
                </div>
                <div class="step-item">
                    <span class="step-number">02</span>
                    <h3 class="step-title">Recherche</h3>
                    <p class="step-desc">Parcourez le catalogue filtrable par catégorie (CPU, RAM, OS, Stockage).</p>
                </div>
                <div class="step-item">
                    <span class="step-number">03</span>
                    <h3 class="step-title">Réservation</h3>
                    <p class="step-desc">Sélectionnez vos dates. Le système vérifie instantanément la disponibilité.</p>
                </div>
                <div class="step-item">
                    <span class="step-number">04</span>
                    <h3 class="step-title">Validation</h3>
                    <p class="step-desc">Le responsable valide votre demande. Vous recevez une notification immédiate.</p>
                </div>
            </div>
        </div>

        <div class="section-container">
            <h2 class="section-title center-text">Un Ecosystème Complet</h2>
            <p class="section-subtitle center-text">Des droits et permissions adaptés à chaque acteur du Data Center.</p>

            <div class="grid-4">
                <div class="glass-card">
                    <span class="feature-icon">👀</span>
                    <h3>Invité</h3>
                    <p style="color: #cbd5e0; flex-grow: 1;">Accès lecture seule au catalogue pour découvrir
                        les ressources disponibles avant inscription.</p>
                </div>
                <div class="glass-card"
                    style="border-color: rgba(6, 182, 212, 0.4); box-shadow: 0 0 20px rgba(6,182,212,0.1);">
                    <span class="feature-icon">💻</span>
                    <h3 style="color: #06b6d4;">Utilisateur Interne</h3>
                    <p style="color: #cbd5e0; flex-grow: 1;">Ingénieurs & Chercheurs. Réservation, historique
                        personnel et suivi de statut en temps réel.</p>
                </div>
                <div class="glass-card">
                    <span class="feature-icon">🛡️</span>
                    <h3>Responsable</h3>
                    <p style="color: #cbd5e0; flex-grow: 1;">Gestionnaire de parc. Validation des demandes,
                        mise en maintenance et arbitrage.</p>
                </div>
                <div class="glass-card">
                    <span class="feature-icon">⚙️</span>
                    <h3>Administrateur</h3>
                    <p style="color: #cbd5e0; flex-grow: 1;">Super-utilisateur. Gestion des utilisateurs,
                        rôles, permissions et statistiques globales.</p>
                </div>
            </div>
        </div>

        <div class="section-container">
            <div class="split-layout" style="flex-direction: row-reverse;">
                <div class="text-block">
                    <h2 class="section-title">Technologie & Sécurité</h2>
                    <p style="color: #cbd5e0; line-height: 1.8; margin-bottom: 20px;">
                        Développé avec les standards modernes pour garantir robustesse et évolutivité.
                    </p>

                    <div class="tech-stack">
                        <div class="tech-item">
                            <div style="font-size: 2rem;">🔴</div>
                            <span>Laravel 10+</span>
                        </div>
                        <div class="tech-item">
                            <div style="font-size: 2rem;">🐬</div>
                            <span>MySQL</span>
                        </div>
                        <div class="tech-item">
                            <div style="font-size: 2rem;">🔒</div>
                            <span>Bcrypt</span>
                        </div>
                        <div class="tech-item">
                            <div style="font-size: 2rem;">🎨</div>
                            <span>CSS Custom</span>
                        </div>
                    </div>

                    <div style="margin-top: 40px;">
                        <div class="faq-item">
                            <div class="faq-question">Authentification Sécurisée</div>
                            <div class="faq-answer">Utilisation des Middlewares Laravel et hachage des mots de passe.
                                Protection CSRF incluse.</div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">Traçabilité (Logs)</div>
                            <div class="faq-answer">Chaque action critique (Réservation, Validation, Suppression) est
                                enregistrée.</div>
                        </div>
                    </div>
                </div>
                <div class="image-block">
                    <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                        alt="Cyber Security" class="image-card">
                </div>
            </div>
        </div>

        <div class="section-container">
            <h2 class="section-title center-text">Questions Fréquentes</h2>
            <div style="max-width: 800px; margin: 0 auto;">
                <div class="faq-item">
                    <div class="faq-question">Puis-je annuler une réservation approuvée ? <span>+</span></div>
                    <div class="faq-answer">Oui, tant que la période de réservation n'a pas commencé. Sinon, vous devez
                        contacter un responsable.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">Comment savoir si ma demande est acceptée ? <span>+</span></div>
                    <div class="faq-answer">Le statut de votre demande passera de "En attente" à "Validée" dans votre
                        tableau de bord.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">Que faire en cas de panne matérielle ? <span>+</span></div>
                    <div class="faq-answer">Utilisez le bouton "Signaler un incident" disponible sur la fiche de la
                        ressource.</div>
                </div>
            </div>
        </div>

        @guest
            <div class="cta-section">
                <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Prêt à optimiser votre infrastructure ?</h2>
                <p style="color: #cbd5e0; margin-bottom: 40px;">Rejoignez la plateforme dès aujourd'hui.</p>
                <a href="{{ route('register') }}" class="btn-hero btn-primary-glow">Créer un compte maintenant</a>
            </div>
        @endguest

    </div>

@endsection