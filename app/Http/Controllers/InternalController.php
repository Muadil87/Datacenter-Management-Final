<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Resource;
use App\Models\User;
use Carbon\Carbon;

class InternalController extends Controller
{
    public function index() {
        // Get user's reservations for utilization data
        $userReservations = Reservation::where('user_id', Auth::id())
                            ->with('resource')
                            ->get();
        
        // Get IDs of currently occupied resources (approved reservations active NOW)
        $now = now();
        $occupiedResourceIds = Reservation::where('status', 'approved')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->pluck('resource_id')
            ->unique()
            ->toArray();
        
        // Get all available resources (excluding maintenance and currently occupied)
        $resources = Resource::where('state', '!=', 'maintenance')
            ->whereNotIn('id', $occupiedResourceIds)
            ->get();
        
        // Calculate utilization metrics
        $utilizationMetrics = $this->getUtilizationMetrics($userReservations);
        
        // Get reservation status breakdown
        $statusBreakdown = $this->getStatusBreakdown($userReservations);
        
        // Get activity timeline
        $activityTimeline = $this->getActivityTimeline($userReservations);
        
        return view('internal.dashboard', compact(
            'resources',
            'utilizationMetrics',
            'statusBreakdown',
            'activityTimeline',
            'userReservations'
        ));
    }
    
 public function myReservations(Request $request)
    {
        $userId = Auth::id();

        // 1. Début de la requête : On prend les réservations de l'utilisateur
        $query = Reservation::where('user_id', $userId)->with('resource');

        // 2. Moteur de Recherche (par nom de ressource)
        if ($request->filled('search')) {
            $search = $request->search;
            // On utilise whereHas pour chercher dans la table liée 'resources'
            $query->whereHas('resource', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // 3. Filtre par Statut
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // 4. Exécuter la requête
        $reservations = $query->orderBy('created_at', 'desc')->get();

        // (Optionnel) Recalculer les stats si vous les utilisez dans la vue
        /* $stats = [
            'total' => Reservation::where('user_id', $userId)->count(),
            // ... autres stats
        ];
        */

        // 5. Renvoyer la vue avec les résultats filtrés
        return view('internal.reservations', compact('reservations'));
    }

    // Formulaire de rapport d'incident (seulement pour ressources réservées)
    public function reportIncidentForm(Request $request) {
        // Récupérer les ressources réservées par l'utilisateur
        $resources = Reservation::where('user_id', Auth::id())
            ->pluck('resource_id')
            ->toArray();
        
        $resources = Resource::whereIn('id', $resources)->get();
        
        // Si un resource_id est passé en param, on le pré-sélectionne
        $preSelectedResource = $request->query('resource_id');
        
        return view('internal.incident.report', compact('resources', 'preSelectedResource'));
    }

    // Soumettre un rapport d'incident
    public function reportIncident(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'resource_id' => 'required|exists:resources,id',
            'priority' => 'required|in:low,medium,high,critical',
        ]);
        
        // Vérifier que l'utilisateur a réservé cette ressource
        $hasReservation = Reservation::where('user_id', Auth::id())
            ->where('resource_id', $request->resource_id)
            ->exists();
        
        if (!$hasReservation) {
            return back()->withErrors(['resource_id' => 'Vous ne pouvez signaler un incident que pour une ressource que vous avez réservée.']);
        }

        $incident = \App\Models\Incident::create([
            'title' => $request->title,
            'description' => $request->description,
            'resource_id' => $request->resource_id,
            'user_id' => Auth::id(),
            'priority' => $request->priority,
            'status' => 'ouvert'
        ]);

        // Notifier les admins et les managers
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::notify(
                $admin->id,
                'Nouvel Incident Signalé',
                'Un nouvel incident a été signalé par ' . Auth::user()->name,
                'incident_reported',
                $incident->id,
                'Incident'
            );
        }

        // Notifier les managers aussi
        $managers = User::where('role', 'manager')->get();
        foreach ($managers as $manager) {
            \App\Models\Notification::notify(
                $manager->id,
                'Nouvel Incident Signalé',
                'Un nouvel incident a été signalé par ' . Auth::user()->name,
                'incident_reported',
                $incident->id,
                'Incident'
            );
        }

        return back()->with('success', 'Incident signalé avec succès. Les administrateurs et managers en ont été notifiés.');
    }

    // Afficher mes incidents signalés
    public function myIncidents() {
        $incidents = \App\Models\Incident::where('user_id', Auth::id())
                        ->with('resource')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return view('internal.incident.my-incidents', compact('incidents'));
    }
    
    private function getUtilizationMetrics($reservations) {
        $total = $reservations->count();
        $approved = $reservations->where('status', 'approved')->count();
        $pending = $reservations->where('status', 'pending')->count();
        $finished = $reservations->where('status', 'finished')->count();
        $refused = $reservations->where('status', 'refused')->count();
        
        $utilizationRate = $total > 0 ? round(($approved / $total) * 100) : 0;
        
        return [
            'total' => $total,
            'approved' => $approved,
            'pending' => $pending,
            'finished' => $finished,
            'refused' => $refused,
            'utilizationRate' => $utilizationRate,
        ];
    }
    
    private function getStatusBreakdown($reservations) {
        return [
            'labels' => ['Approved', 'Pending', 'Finished', 'Refused'],
            'data' => [
                $reservations->where('status', 'approved')->count(),
                $reservations->where('status', 'pending')->count(),
                $reservations->where('status', 'finished')->count(),
                $reservations->where('status', 'refused')->count(),
            ],
            'colors' => ['#27ae60', '#f39c12', '#3498db', '#e74c3c'],
        ];
    }
    
    private function getActivityTimeline($reservations) {
        $last12Months = [];
        $now = Carbon::now();
        
        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $count = $reservations->filter(function($r) use ($month) {
                return Carbon::parse($r->created_at)->format('Y-m') === $month->format('Y-m');
            })->count();
            $last12Months[$month->format('M Y')] = $count;
        }
        
        return [
            'labels' => array_keys($last12Months),
            'data' => array_values($last12Months),
        ];
    }
}

 
