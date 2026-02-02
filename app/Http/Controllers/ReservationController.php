<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Resource;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    // --- PARTIE UTILISATEUR INTERNE ---

    // 1. Afficher le formulaire de réservation pour une ressource spécifique
    public function create($resource_id)
    {
        $resource = Resource::findOrFail($resource_id);
        return view('reservations.create', compact('resource'));
    }

    // 2. Traiter la demande de réservation
    public function store(Request $request, $resource_id)
    {
        $request->validate([
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'justification' => 'required|string|max:1000',
        ]);

        // Vérification des conflits (Overlap check)
        // On cherche s'il existe une réservation APPROUVÉE qui chevauche les dates demandées
        $conflict = Reservation::where('resource_id', $resource_id)
            ->where('status', 'approved')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_date', '<', $request->start_date)
                            ->where('end_date', '>', $request->end_date);
                      });
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['date' => 'Cette ressource est déjà réservée sur cette période.']);
        }

        $resource = Resource::findOrFail($resource_id);
        
        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'resource_id' => $resource_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'justification' => $request->justification,
            'status' => 'pending' // En attente par défaut
        ]);

        // Notifier l'utilisateur que sa demande a été envoyée
        Notification::notify(
            Auth::id(),
            '📝 Demande de Réservation Créée',
            'Votre demande de réservation pour ' . $resource->name . ' a été envoyée pour approbation.',
            'reservation_created',
            $reservation->id,
            'Reservation'
        );

        // Notifier le responsable (manager) si la ressource en a un
        if ($resource->responsible_id) {
            Notification::notify(
                $resource->responsible_id,
                '⏳ Nouvelle Demande de Réservation',
                Auth::user()->name . ' a demandé une réservation pour ' . $resource->name . '.',
                'reservation_pending',
                $reservation->id,
                'Reservation'
            );
        } else {
            // Si la ressource n'a pas de manager assigné, notifier tous les managers
            $managers = User::where('role', 'manager')->get();
            foreach ($managers as $manager) {
                Notification::notify(
                    $manager->id,
                    '⏳ Nouvelle Demande de Réservation',
                    Auth::user()->name . ' a demandé une réservation pour ' . $resource->name . '.',
                    'reservation_pending',
                    $reservation->id,
                    'Reservation'
                );
            }
        }

        return redirect()->route('internal.dashboard')->with('success', 'Demande envoyée avec succès.');
    }

    // --- PARTIE MANAGER (RESPONSABLE) ---

    // 3. Valider ou Refuser une demande
    public function handleRequest(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        // Sécurité : Vérifier si le manager est bien responsable de cette ressource (Optionnel selon tes règles)
        // if($reservation->resource->responsible_id != Auth::id()) abort(403);

        $action = $request->input('action'); // 'approve' ou 'refuse'

        if ($action === 'approve') {
            // Re-vérification de conflit de dernière minute
            // (Code de vérification conflit identique à store() à insérer ici idéalement)
            
            // IMPORTANT: Utiliser update() pour déclencher l'Observer
            $reservation->update(['status' => 'approved']);
            
            // Mettre à jour directement le state de la ressource à "occupied"
            $now = now();
            if ($reservation->start_date <= $now && $reservation->end_date >= $now) {
                $reservation->resource()->update(['state' => 'occupied']);
            }
            
            // Notifier l'utilisateur que sa demande a été approuvée
            Notification::notify(
                $reservation->user_id,
                '✅ Réservation Approuvée',
                'Votre réservation pour ' . $reservation->resource->name . ' a été approuvée!',
                'reservation_approved',
                $reservation->id,
                'Reservation'
            );
            
        } elseif ($action === 'refuse') {
            // IMPORTANT: Utiliser update() pour déclencher l'Observer
            $reservation->update(['status' => 'refused']);
            
            // Si la ressource était occupée, la rendre disponible
            $reservation->resource()->update(['state' => 'available']);
            
            // Notifier l'utilisateur que sa demande a été refusée
            Notification::notify(
                $reservation->user_id,
                '❌ Réservation Refusée',
                'Votre demande de réservation pour ' . $reservation->resource->name . ' a été refusée.',
                'reservation_refused',
                $reservation->id,
                'Reservation'
            );
        }

        return back()->with('success', 'La réservation a été mise à jour.');
    }

    public function getReservations($resource_id)
    {
        $reservations = Reservation::where('resource_id', $resource_id)
            ->where('status', 'approved')
            ->with('user')
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function($res) {
                return [
                    'user_name' => $res->user->name,
                    'user_email' => $res->user->email,
                    'start_date' => $res->start_date,
                    'end_date' => $res->end_date
                ];
            });
        
        return response()->json(['reservations' => $reservations]);
    }
}
