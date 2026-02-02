<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// --- Imports pour ta Partie D (Observers) ---
use App\Models\Resource;
use App\Models\Reservation;
use App\Models\Incident;
use App\Observers\ResourceObserver;
use App\Observers\ReservationObserver;
use App\Observers\IncidentObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // --- ACTIVATION DES ESPIONS (PARTIE D) ---

        // 1. Espionner les Ressources (Partie B)
        // Vérifie si la classe existe pour éviter le crash si le merge n'est pas complet
        if (class_exists(Resource::class) && class_exists(ResourceObserver::class)) {
            Resource::observe(ResourceObserver::class);
        }

        // 2. Espionner les Réservations (Partie C)
        if (class_exists(Reservation::class) && class_exists(ReservationObserver::class)) {
            Reservation::observe(ReservationObserver::class);
        }

        // 3. Espionner tes Incidents (Partie D)
        if (class_exists(Incident::class) && class_exists(IncidentObserver::class)) {
            Incident::observe(IncidentObserver::class);
        }
    }
}