<?php

namespace App\Observers;

use App\Models\Reservation;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class ReservationObserver
{
    /**
     * Handle the Reservation "created" event.
     */
    public function created(Reservation $reservation): void
    {
        Log::create([
            'user_id' => $reservation->user_id ?? Auth::id() ?? 1,
            'action'  => 'Création Réservation',
            'details' => "Nouvelle réservation pour la ressource ID: {$reservation->resource_id}",
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Handle the Reservation "updated" event.
     */
    public function updated(Reservation $reservation): void
    {
        // Récupérer les anciennes valeurs
        $oldStatus = $reservation->getOriginal('status');
        $newStatus = $reservation->status;

        Log::create([
            'user_id' => Auth::id() ?? 1,
            'action'  => 'Mise à jour Réservation',
            'details' => "Réservation ID: {$reservation->id} mise à jour (Statut: {$oldStatus} → {$newStatus})",
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Handle the Reservation "deleted" event.
     */
    public function deleted(Reservation $reservation): void
    {
        // Si la réservation supprimée était approuvée, vérifier si la ressource peut redevenir disponible
        if ($reservation->status === 'approved') {
            $hasActiveReservations = Reservation::where('resource_id', $reservation->resource_id)
                ->where('status', 'approved')
                ->exists();

            if (!$hasActiveReservations) {
                $reservation->resource()->update(['state' => 'available']);
            }
        }

        Log::create([
            'user_id' => Auth::id() ?? 1,
            'action'  => 'Annulation Réservation',
            'details' => "Réservation ID: {$reservation->id} supprimée (Statut: {$reservation->status})",
            'ip_address' => request()->ip()
        ]);
    }
}
