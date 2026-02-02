@extends('layout')

@section('content')
    <div style="max-width: 900px; margin: auto;">

        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #ffffff; margin-bottom: 10px;">Charte d'utilisation des ressources</h1>
            <p style="color: #a0aec0; font-size: 1.1em;">Data Center Universitaire</p>
        </div>

        <div
            style="background: rgba(30, 41, 59, 0.6); padding: 40px; border-radius: 8px; border: 1px solid rgba(148, 163, 184, 0.2); backdrop-filter: blur(10px);">

            <p style="margin-bottom: 20px; color: #e0e7ff; line-height: 1.8;">
                L'accès aux ressources informatiques du Data Center (serveurs, machines virtuelles, stockage) est soumis à
                l'acceptation et au respect strict des règles énoncées ci-dessous. Tout manquement pourra entraîner la
                suspension immédiate du compte.
            </p>

            <hr style="border: 0; border-top: 1px solid rgba(148, 163, 184, 0.3); margin: 20px 0;">

            <h3 style="color: #06b6d4; margin-bottom: 10px;">1. Accès et Sécurité des Comptes</h3>
            <ul style="margin-bottom: 20px; padding-left: 20px; line-height: 1.8; color: #cbd5e1;">
                <li><strong>Compte personnel :</strong> L'accès est strictement personnel. Il est interdit de prêter ses
                    identifiants à un tiers.</li>
                <li><strong>Authentification :</strong> Toute action réalisée avec votre compte est réputée avoir été
                    effectuée par vous.</li>
                <li><strong>Signalement :</strong> Tout soupçon de compromission de mot de passe doit être immédiatement
                    signalé à l'administrateur.</li>
            </ul>

            <h3 style="color: #06b6d4; margin-bottom: 10px;">2. Utilisation des Ressources</h3>
            <ul style="margin-bottom: 20px; padding-left: 20px; line-height: 1.8; color: #cbd5e1;">
                <li><strong>Usage pédagogique et recherche :</strong> Les ressources sont réservées aux travaux pratiques,
                    projets de recherche et développements académiques.</li>
                <li><strong>Activités interdites :</strong>
                    <ul style="margin-top: 5px; color: #f87171;">
                        <li>⛔ Minage de crypto-monnaies.</li>
                        <li>⛔ Hébergement de contenus illégaux ou piratés.</li>
                        <li>⛔ Attaques réseau (DDoS, scans de ports) vers l'intérieur ou l'extérieur.</li>
                    </ul>
                </li>
                <li><strong>Libération des ressources :</strong> Une ressource réservée mais non utilisée doit être libérée
                    immédiatement pour profiter aux autres utilisateurs.</li>
            </ul>

            <h3 style="color: #06b6d4; margin-bottom: 10px;">3. Données et Responsabilité</h3>
            <ul style="margin-bottom: 20px; padding-left: 20px; line-height: 1.8; color: #cbd5e1;">
                <li><strong>Sauvegardes :</strong> Le Data Center n'assure pas de sauvegarde automatique des données
                    utilisateurs stockées sur les VM. Il est de votre responsabilité de sauvegarder vos travaux en local.
                </li>
                <li><strong>Confidentialité :</strong> L'administration se réserve le droit d'inspecter les machines
                    virtuelles en cas de suspicion d'activité malveillante.</li>
            </ul>

            <div
                style="border-left: 5px solid #f87171; background-color: rgba(248, 113, 113, 0.1); color: #fca5a5; padding: 20px; border-radius: 4px; margin-bottom: 20px;">
                <strong>Sanctions :</strong> En cas de non-respect de cette charte, l'administrateur se réserve le droit de
                :
                <ol style="margin-left: 20px; margin-top: 10px;">
                    <li>Suspendre temporairement les réservations en cours.</li>
                    <li>Désactiver définitivement le compte utilisateur.</li>
                    <li>Signaler les infractions graves à la direction de l'établissement.</li>
                </ol>
            </div>

            <div style="margin-top: 30px; text-align: center; font-size: 0.9em; color: #94a3b8;">
                Dernière mise à jour : Janvier 2026 — L'équipe technique du Data Center.
            </div>

        </div>
    </div>
@endsection