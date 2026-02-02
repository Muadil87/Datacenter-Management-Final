<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Log;
use App\Models\User;
use App\Models\Resource;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index() {
        // Récupération des compteurs depuis la base de données
        $totalIncidents = Incident::count();
        $totalLogs = Log::count();
        $incidentsResolu = Incident::where('status', 'resolu')->count();
        $incidentsEnCours = Incident::where('status', 'en_cours')->count();
        $incidentsOuverts = Incident::where('status', 'ouvert')->count();
        
        $incidentsNonResolu = $totalIncidents - $incidentsResolu;
        
        $percentOuvert = 0;
        $percentResolu = 0;

        if ($totalIncidents > 0) {
            $percentResolu = ($incidentsResolu / $totalIncidents) * 100;
            $percentOuvert = 100 - $percentResolu;
        }

        // Données Utilisateurs et Ressources
        $totalUsers = User::count();
        $totalResources = Resource::count();
        $resourcesOccupied = Resource::where('state', 'occupied')->count();
        $resourcesMaintenance = Resource::where('state', 'maintenance')->count();
        $users = User::where('id', '!=', auth()->id())->get();

        // Calcul du taux d'occupation basé sur les réservations du mois actuel
        $tauxOccupation = 0;
        if ($totalResources > 0) {
            $now = Carbon::now();
            $monthStart = $now->clone()->startOfMonth();
            $monthEnd = $now->clone()->endOfMonth();
            $daysInMonth = $monthEnd->day;
            $totalCapacity = $totalResources * $daysInMonth;

            $occupiedDays = Reservation::whereIn('status', ['approved', 'pending'])
                ->where(function ($query) use ($monthStart, $monthEnd) {
                    $query->whereBetween('start_date', [$monthStart, $monthEnd])
                          ->orWhereBetween('end_date', [$monthStart, $monthEnd])
                          ->orWhere(function ($q) use ($monthStart, $monthEnd) {
                              $q->where('start_date', '<=', $monthStart)
                                ->where('end_date', '>=', $monthEnd);
                          });
                })
                ->get()
                ->sum(function ($reservation) use ($monthStart, $monthEnd) {
                    // Calculate days overlap with current month
                    $start = max($reservation->start_date, $monthStart);
                    $end = min($reservation->end_date, $monthEnd);
                    return Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1;
                });
            
            // Calculate percentage with 2 decimal places
            if ($totalCapacity > 0) {
                $tauxOccupation = ($occupiedDays / $totalCapacity) * 100;
                $tauxOccupation = round($tauxOccupation, 2);
            }
        }

        // Ressource la plus réservée (current month only)
        $now = Carbon::now();
        $monthStart = $now->clone()->startOfMonth();
        $monthEnd = $now->clone()->endOfMonth();
        
        $topRessource = Resource::withCount(['reservations' => function ($query) use ($monthStart, $monthEnd) {
            $query->whereIn('status', ['approved', 'pending'])
                  ->whereBetween('start_date', [$monthStart, $monthEnd])
                  ->orWhere(function ($q) use ($monthStart, $monthEnd) {
                      $q->whereIn('status', ['approved', 'pending'])
                        ->whereBetween('end_date', [$monthStart, $monthEnd]);
                  })
                  ->orWhere(function ($q) use ($monthStart, $monthEnd) {
                      $q->whereIn('status', ['approved', 'pending'])
                        ->where('start_date', '<=', $monthStart)
                        ->where('end_date', '>=', $monthEnd);
                  });
        }])
            ->orderBy('reservations_count', 'desc')
            ->first();
        
        $topRessourceName = $topRessource ? $topRessource->name : "N/A";
        $topRessourceCount = $topRessource ? $topRessource->reservations_count : 0;

        // Envoi à la vue
        return view('stats.dashboard', compact(
            'totalIncidents', 
            'totalLogs', 
            'incidentsResolu',
            'incidentsEnCours',
            'incidentsOuverts',
            'incidentsNonResolu',
            'percentOuvert', 
            'percentResolu',
            'tauxOccupation',
            'topRessourceName',
            'topRessourceCount',
            'totalUsers',
            'totalResources',
            'resourcesOccupied',
            'resourcesMaintenance',
            'users'
        ));
    }
}