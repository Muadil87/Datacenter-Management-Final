<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Resource;
use App\Models\User;
use App\Models\Incident;
use App\Models\Maintenance;
use App\Models\Notification;
use Carbon\Carbon;

class ManagerController extends Controller
{
    public function index() {
        $managerId = Auth::id();
        
        // Get all resources managed by this manager
        $managedResources = Resource::where('responsible_id', $managerId)->pluck('id')->toArray();
        
        // If no resources assigned, show all reservations for testing/setup
        // (In production, this should only show their resources)
        if (empty($managedResources)) {
            // For development: show all reservations if manager has no resources assigned yet
            $pendingReservations = Reservation::where('status', 'pending')
                                        ->with(['user', 'resource'])
                                        ->orderBy('created_at', 'desc')
                                        ->get();

            $approvedReservations = Reservation::where('status', 'approved')
                                        ->with(['user', 'resource'])
                                        ->orderBy('start_date', 'asc')
                                        ->get();
            
            $allReservations = Reservation::with(['user', 'resource'])->get();
            $resources = Resource::all();
        } else {
            // Show only reservations for managed resources
            $pendingReservations = Reservation::whereIn('resource_id', $managedResources)
                                        ->where('status', 'pending')
                                        ->with(['user', 'resource'])
                                        ->orderBy('created_at', 'desc')
                                        ->get();

            $approvedReservations = Reservation::whereIn('resource_id', $managedResources)
                                        ->where('status', 'approved')
                                        ->with(['user', 'resource'])
                                        ->orderBy('start_date', 'asc')
                                        ->get();
            
            $allReservations = Reservation::whereIn('resource_id', $managedResources)
                                    ->with(['user', 'resource'])
                                    ->get();
            $resources = Resource::whereIn('id', $managedResources)->get();
        }

        // Calculate metrics for charts
        $metrics = $this->calculateMetrics($allReservations, $resources);
        
        // Get reservation data for timeline chart
        $reservationTimeline = $this->getReservationTimeline($allReservations);
        
        // Get resource occupancy data
        $occupancyData = $this->getResourceOccupancy($resources);
        
        // Get manager performance metrics
        $performanceMetrics = $this->getPerformanceMetrics($pendingReservations, $approvedReservations);

        return view('manager.dashboard', compact(
            'pendingReservations', 
            'approvedReservations', 
            'managedResources',
            'metrics',
            'reservationTimeline',
            'occupancyData',
            'performanceMetrics'
        ));
    }
    
    private function calculateMetrics($reservations, $resources) {
        $approved = $reservations->where('status', 'approved')->count();
        $pending = $reservations->where('status', 'pending')->count();
        $refused = $reservations->where('status', 'refused')->count();
        $finished = $reservations->where('status', 'finished')->count();
        
        return compact('approved', 'pending', 'refused', 'finished');
    }
    
    private function getReservationTimeline($reservations) {
        $last30Days = [];
        $now = Carbon::now();
        
        for ($i = 29; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $count = $reservations->filter(function($r) use ($date) {
                return Carbon::parse($r->created_at)->format('Y-m-d') === $date->format('Y-m-d');
            })->count();
            $last30Days[$date->format('d M')] = $count;
        }
        
        return [
            'labels' => array_keys($last30Days),
            'data' => array_values($last30Days),
        ];
    }
    
    private function getResourceOccupancy($resources) {
        $occupancy = [];
        
        foreach($resources as $resource) {
            $total = $resource->reservations()->count();
            $occupied = $resource->reservations()->where('status', 'approved')->count();
            $percentage = $total > 0 ? round(($occupied / $total) * 100) : 0;
            
            $occupancy[] = [
                'name' => $resource->name,
                'percentage' => $percentage,
                'total' => $total,
                'occupied' => $occupied,
            ];
        }
        
        return $occupancy;
    }
    
    private function getPerformanceMetrics($pending, $approved) {
        $totalRequests = $pending->count() + $approved->count();
        $approvalRate = $totalRequests > 0 ? round(($approved->count() / $totalRequests) * 100) : 0;
        $avgResponseTime = $pending->count() > 0 ? 'In Progress' : 'Up to date';
        
        return [
            'totalRequests' => $totalRequests,
            'approvalRate' => $approvalRate,
            'avgResponseTime' => $avgResponseTime,
            'pending' => $pending->count(),
            'approved' => $approved->count(),
        ];
    }

    // Formulaire de rapport d'incident (Manager)
    public function reportIncidentForm(Request $request) {
        // Manager peut signaler des incidents pour TOUTES les ressources
        $resources = Resource::all();
        
        // Si un resource_id est passé en param, on le pré-sélectionne
        $preSelectedResource = $request->query('resource_id');
        
        return view('manager.incident.report', compact('resources', 'preSelectedResource'));
    }

    // Soumettre un rapport d'incident (Manager)
    public function reportIncident(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'resource_id' => 'required|exists:resources,id',
            'priority' => 'required|in:low,medium,high,critical',
        ]);
        
        // Pour manager, on ne valide pas qu'il gère la ressource
        // Il peut signaler pour n'importe quelle ressource

        $incident = \App\Models\Incident::create([
            'title' => $request->title,
            'description' => $request->description,
            'resource_id' => $request->resource_id,
            'user_id' => Auth::id(),
            'priority' => $request->priority,
            'status' => 'ouvert'
        ]);

        // Notifier les admins
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

        return back()->with('success', 'Incident signalé avec succès. Les administrateurs en ont été notifiés.');
    }

    /*
      List all incidents reported by internal users AND managers
      Manager sees incidents from internal users AND other managers
     */
    public function listIncidents() {
        $incidents = \App\Models\Incident::whereHas('user', function($query) {
            $query->whereIn('role', ['internal', 'manager']);
        })->with(['user', 'resource'])
          ->orderBy('created_at', 'desc')
          ->get();

        return view('manager.incidents', compact('incidents'));
    }

    /*
      Show incident details
     */
    public function showIncident(\App\Models\Incident $incident) {
        // Verify incident is from internal user or manager
        if (!in_array($incident->user->role, ['internal', 'manager'])) {
            abort(403, 'Unauthorized action.');
        }

        return view('manager.incidents-detail', compact('incident'));
    }

    /*
     Update incident status
     Manager can manage incidents like admin (resolve, etc)
     */
    public function updateIncidentStatus(Request $request, \App\Models\Incident $incident) {
        // Verify incident is from internal user or manager
        if (!in_array($incident->user->role, ['internal', 'manager'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:ouvert,en_traitement,resolu,clos'
        ]);

        $oldStatus = $incident->status;
        $incident->update(['status' => $request->status]);

        // Create notification for admins about status update
        $statusLabels = [
            'ouvert' => 'Open',
            'en_traitement' => 'In Progress',
            'resolu' => 'Resolved',
            'clos' => 'Closed'
        ];

        $oldStatusLabel = $statusLabels[$oldStatus] ?? $oldStatus;
        $newStatusLabel = $statusLabels[$request->status] ?? $request->status;
        
        $notificationMessage = "Incident '{$incident->title}' status updated from {$oldStatusLabel} to {$newStatusLabel}";
        
        // Get all admin users
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'incident_updated',
                'title' => 'Incident Status Updated',
                'message' => $notificationMessage,
                'related_id' => $incident->id,
                'is_read' => false
            ]);
        }

        // Also notify the incident reporter
        Notification::create([
            'user_id' => $incident->user_id,
            'type' => 'incident_status_changed',
            'title' => 'Your Incident Status Changed',
            'message' => "Your incident '{$incident->title}' status has been updated to {$newStatusLabel}",
            'related_id' => $incident->id,
            'is_read' => false
        ]);

        return back()->with('success', 'Statut de l\'incident mis à jour.');
    }

    // Delete incident
     
    public function deleteIncident(\App\Models\Incident $incident) {
        // Verify incident is from internal user
        if ($incident->user->role !== 'internal') {
            abort(403, 'Unauthorized action.');
        }

        $incident->delete();
        return redirect()->route('manager.incidents.list')->with('success', 'Incident supprimé.');
    }



    // List all maintenance scheduled by this manager or available resources

    public function listMaintenance() {
        $managerId = Auth::id();
        
        // Show all resources that can have maintenance
        $resources = Resource::all();
        
        // Get resources currently in maintenance
        $maintenanceResources = Resource::where('state', 'maintenance')->get();

        return view('manager.maintenance', compact('resources', 'maintenanceResources'));
    }

    
     // Create/Schedule maintenance for a resource
     
    public function createMaintenance(Request $request) {
        $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'start_at' => 'required|date_format:Y-m-d\TH:i',
            'end_at' => 'required|date_format:Y-m-d\TH:i|after:start_at',
            'description' => 'nullable|string|max:1000'
        ]);

        $resource = Resource::findOrFail($request->resource_id);

        $now = Carbon::now();
        $startAt = Carbon::parse($request->start_at);
        $endAt = Carbon::parse($request->end_at);

        $status = 'scheduled';
        if ($startAt->lte($now) && $endAt->gt($now)) {
            $status = 'in_progress';
            $resource->update(['state' => 'maintenance']);
        } elseif ($endAt->lte($now)) {
            $status = 'completed';
            $resource->update(['state' => 'available']);
        }

        // Create maintenance log with dates
        Maintenance::create([
            'resource_id' => $request->resource_id,
            'title' => 'Maintenance - ' . $resource->name,
            'reason' => $request->description,
            'created_by' => Auth::id(),
            'status' => $status,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at
        ]);

        // Notify admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::notify(
                $admin->id,
                'Maintenance Planifiée',
                'Une maintenance a été planifiée pour ' . $resource->name . ' du ' . date('d/m/Y H:i', strtotime($request->start_at)) . ' au ' . date('d/m/Y H:i', strtotime($request->end_at)),
                'maintenance_scheduled',
                $resource->id,
                'Resource'
            );
        }

        return back()->with('success', 'Maintenance planifiée avec succès. La ressource passera en maintenance à la date prévue.');
    }
}
