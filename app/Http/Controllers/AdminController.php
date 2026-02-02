<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resource;
use App\Models\Notification;
use App\Models\Reservation;
use Carbon\Carbon;

class AdminController extends Controller
{
    // Display dashboard with statistics
    public function index() {
        $users = User::where('id', '!=', auth()->id())->get();
        $totalUsers = User::count();
        $totalResources = Resource::count();
        $now = now();
        
        $resourcesOccupied = Resource::where('state', 'occupied')
            ->orWhereHas('reservations', function ($query) use ($now) {
                $query->where('status', 'approved')
                      ->where('start_date', '<=', $now)
                      ->where('end_date', '>=', $now);
            })
            ->distinct()
            ->count();
        
        // Ressources en maintenance
        $resourcesMaintenance = Resource::where('state', 'maintenance')->count();
        
        // Stats incidents et logs
        $totalIncidents = \App\Models\Incident::count();
        $totalLogs = \App\Models\Log::count();
        $incidentsResolu = \App\Models\Incident::where('status', 'resolu')->count();
        $incidentsEnCours = \App\Models\Incident::where('status', 'en_cours')->count();
        $incidentsOuverts = \App\Models\Incident::where('status', 'ouvert')->count();
        
        // Calcul des pourcentages
        $percentOuvert = 0;
        $percentResolu = 0;
        if ($totalIncidents > 0) {
            $percentResolu = ($incidentsResolu / $totalIncidents) * 100;
            $percentOuvert = 100 - $percentResolu;
        }
        
        // Taux d'occupation des ressources
        $tauxOccupation = 0;
        if ($totalResources > 0) {
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
                    $start = max($reservation->start_date, $monthStart);
                    $end = min($reservation->end_date, $monthEnd);
                    return Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1;
                });
            
            if ($totalCapacity > 0) {
                $tauxOccupation = ($occupiedDays / $totalCapacity) * 100;
                $tauxOccupation = round($tauxOccupation, 2);
            }
        }
        
        // Ressource la plus réservée (mois actuel)
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

        return view('admin.dashboard', compact(
            'users', 'totalUsers', 'totalResources', 'resourcesOccupied', 'resourcesMaintenance',
            'totalIncidents', 'totalLogs', 'incidentsResolu', 'incidentsEnCours', 'incidentsOuverts',
            'percentOuvert', 'percentResolu', 'tauxOccupation', 'topRessourceName', 'topRessourceCount'
        ));
    }

    // Update user role
    public function updateRole(Request $request, User $user) {
        $request->validate([
            'role' => 'required|in:admin,manager,internal',
        ]);

        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->withErrors(['error' => 'Impossible de changer votre propre rôle d\'admin.']);
        }

        $oldRole = $user->role;
        $user->role = $request->role;
        $user->save();

        // Create notification for user about role change
        Notification::create([
            'user_id' => $user->id,
            'type' => 'role_changed',
            'title' => 'Your Role Has Been Updated',
            'message' => "Your role has been changed from {$oldRole} to {$request->role}",
            'is_read' => false
        ]);

        return back()->with('success', 'Rôle mis à jour avec succès.');
    }

    // Activate user account
    public function activate(User $user) {
        $user->is_active = true;
        $user->save();

        // Notifier l'utilisateur que son compte a été activé
        Notification::notify(
            $user->id,
            'Compte Activé',
            'Votre compte a été activé par un administrateur. Vous pouvez maintenant vous connecter!',
            'account_activated',
            $user->id,
            'User'
        );

        return back()->with('success', 'Compte activé avec succès.');
    }

    // Deactivate user account
    public function deactivate(User $user) {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Impossible de désactiver votre propre compte admin.']);
        }

        $user->is_active = false;
        $user->save();

        return back()->with('success', 'Compte désactivé.');
    }

    // Approve user registration
    public function approveRegistration(User $user) {
        if ($user->email_verified_at) {
            return back()->withErrors(['error' => 'Cet utilisateur a déjà été approuvé.']);
        }

        $user->email_verified_at = now();
        $user->is_active = true;
        $user->save();

        // Create notification for user
        Notification::create([
            'user_id' => $user->id,
            'type' => 'registration_approved',
            'title' => 'Registration Approved',
            'message' => 'Your registration has been approved! You can now access all features of the platform.',
            'is_read' => false
        ]);

        return back()->with('success', 'Enregistrement de l\'utilisateur approuvé et compte activé.');
    }

    // Delete user account
    public function destroy(User $user) {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Impossible de supprimer votre propre compte admin.']);
        }

        $user->delete();

        return back()->with('success', 'Utilisateur supprimé définitivement.');
    }

    // Assign manager to resource
    public function assignManager(Request $request, Resource $resource) {
        $request->validate([
            'manager_id' => 'nullable|exists:users,id'
        ]);

        $resource->responsible_id = $request->manager_id;
        $resource->save();

        return back()->with('success', 'Gestionnaire assigné à la ressource avec succès.');
    }

    // Display maintenance calendar
    public function maintenanceCalendar() {
        $maintenances = \App\Models\Maintenance::with('resource')->get();
        $resources = Resource::all();
        
        // Convertir en format FullCalendar
        $events = $maintenances->map(function ($maintenance) {
            return [
                'id' => $maintenance->id,
                'title' => 'Maintenance: ' . $maintenance->resource->name,
                'start' => $maintenance->start_at,
                'end' => $maintenance->end_at,
                'backgroundColor' => $maintenance->status === 'completed' ? '#27ae60' : '#e74c3c',
                'borderColor' => $maintenance->status === 'completed' ? '#229954' : '#c0392b',
                'resource_id' => $maintenance->resource_id,
                'reason' => $maintenance->reason,
                'status' => $maintenance->status
            ];
        })->toArray();

        return view('admin.maintenance.calendar', compact('events', 'resources', 'maintenances'));
    }

    // Display schedule form page
    public function maintenanceScheduleForm() {
        $resources = Resource::all();
        return view('admin.maintenance.create', compact('resources'));
    }

    // 8. MAINTENANCE SCHEDULING - Créer une maintenance
    public function scheduleMaintenance(Request $request) {
        $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'title' => 'required|string|max:255',
            'reason' => 'required|string|max:500',
            'start_at' => 'required|date|after_or_equal:now',
            'end_at' => 'required|date|after:start_at',
        ]);

        $resource = Resource::findOrFail($request->resource_id);
        
        // Créer la maintenance
        $maintenance = \App\Models\Maintenance::create([
            'resource_id' => $request->resource_id,
            'title' => $request->title,
            'reason' => $request->reason,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'created_by' => auth()->id(),
            'status' => 'scheduled'
        ]);

        // Mettre à jour l'état de la ressource
        $resource->update(['state' => 'maintenance']);

        // Notifier les managers responsables
        if ($resource->responsible_id) {
            Notification::notify(
                $resource->responsible_id,
                'Maintenance Programmée',
                'Une maintenance a été programmée pour ' . $resource->name,
                'maintenance_scheduled',
                $maintenance->id,
                'Maintenance'
            );
        }

        return back()->with('success', 'Maintenance programmée avec succès.');
    }

    // 9. UPDATE MAINTENANCE STATUS
    public function updateMaintenanceStatus(Request $request, \App\Models\Maintenance $maintenance) {
        $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,cancelled'
        ]);

        $oldStatus = $maintenance->status;
        $maintenance->update(['status' => $request->status]);

        // Si maintenance terminée, restaurer l'état de la ressource
        if ($request->status === 'completed') {
            $maintenance->resource->update(['state' => 'available']);
        }

        return back()->with('success', 'Statut de maintenance mis à jour.');
    }

    // 10. INCIDENT HISTORY & STATISTICS
    public function incidentHistory() {
        $incidents = \App\Models\Incident::with('user', 'resource')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $incidentStats = [
            'total' => \App\Models\Incident::count(),
            'ouvert' => \App\Models\Incident::where('status', 'ouvert')->count(),
            'en_cours' => \App\Models\Incident::where('status', 'en_cours')->count(),
            'resolu' => \App\Models\Incident::where('status', 'resolu')->count(),
        ];

        // Stats par mois
        $incidentsByMonth = \App\Models\Incident::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(12)
            ->get();

        return view('admin.incident.history', compact('incidents', 'incidentStats', 'incidentsByMonth'));
    }

    // USER MANAGEMENT - VERIFICATION AND APPROVAL
    public function usersManagement()
    {
        // Exclude rejected users (those with rejected_at set)
        $pendingUsers = User::where('is_active', false)
            ->whereNull('rejected_at')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $approvedUsers = User::where('is_active', true)
            ->whereNull('rejected_at')
            ->orderBy('updated_at', 'desc')
            ->get();
        
        $allUsers = User::whereNull('rejected_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users', compact('pendingUsers', 'approvedUsers', 'allUsers'));
    }

    public function approveUser($userId)
    {
        $user = User::findOrFail($userId);
        
        $user->update([
            'is_active' => true,
            'role' => 'internal', // Set role to 'internal' upon approval
        ]);

        \Log::info("User approved by admin: {$user->email}");

        return redirect()->route('admin.users')
            ->with('success', "User {$user->email} has been approved as Internal!");
    }

    public function rejectUser($userId)
    {
        $user = User::findOrFail($userId);
        $email = $user->email;
        
        $user->update(['rejected_at' => now()]);

        \Log::info("User rejected by admin: {$email}");

        return redirect()->route('admin.users')
            ->with('success', "User {$email} has been rejected.");
    }

    public function deactivateUser($userId)
    {
        $user = User::findOrFail($userId);
        
        $user->update(['is_active' => false]);

        \Log::info("User deactivated by admin: {$user->email}");

        return redirect()->route('admin.users')
            ->with('success', "User {$user->email} has been deactivated.");
    }
}