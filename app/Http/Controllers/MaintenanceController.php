<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Incident;
use App\Models\Notification;
use App\Models\Maintenance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        // Only tech and admin can access maintenance page
        if (!auth()->check() || !auth()->user()->canAccessMaintenance()) {
            abort(403, 'Unauthorized access.');
        }

        $this->processDueMaintenances();

        $query = Resource::with('category')->where('state', 'maintenance');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $resources = $query->get();

        return view('resources.maintenance', compact('resources'));
    }

    public function ajaxFilter(Request $request)
    {
        // Only tech and admin can filter maintenance
        if (!auth()->check() || !auth()->user()->canAccessMaintenance()) {
            abort(403, 'Unauthorized access.');
        }

        $this->processDueMaintenances();

        $query = Resource::with('category')->where('state', 'maintenance');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category') && $request->category !== 'ALL') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $resources = $query->get();
        return view('resources.partials.grid', compact('resources'));
    }
    
    public function resolve($id)
    {
        // Only tech and admin can resolve maintenance
        if (!auth()->check() || !auth()->user()->canAccessMaintenance()) {
            abort(403, 'Unauthorized action.');
        }

        $resource = Resource::findOrFail($id);
        $resource->update(['state' => 'available']);

        // Mark related incidents as resolved when resource is repaired
        $incidents = Incident::where('resource_id', $resource->id)
            ->whereNotIn('status', ['resolu', 'clos'])
            ->get();

        foreach ($incidents as $incident) {
            $incident->update(['status' => 'resolu']);

            Notification::notify(
                $incident->user_id,
                '✅ Incident Résolu',
                'Votre incident sur ' . $resource->name . ' a été résolu (maintenance terminée).',
                'incident_resolved',
                $incident->id,
                'Incident'
            );
        }
        
        return redirect()->back()->with('success', 'Resource repaired and available again!');
    }

    private function processDueMaintenances(): void
    {
        $now = Carbon::now();

        $startingMaintenances = Maintenance::where('status', 'scheduled')
            ->where('start_at', '<=', $now)
            ->where('end_at', '>', $now)
            ->get();

        foreach ($startingMaintenances as $maintenance) {
            $resource = Resource::find($maintenance->resource_id);
            if ($resource) {
                $resource->update(['state' => 'maintenance']);
                $maintenance->update(['status' => 'in_progress']);
            }
        }

        $endingMaintenances = Maintenance::whereIn('status', ['scheduled', 'in_progress'])
            ->where('end_at', '<=', $now)
            ->get();

        foreach ($endingMaintenances as $maintenance) {
            $resource = Resource::find($maintenance->resource_id);
            if ($resource) {
                $resource->update(['state' => 'available']);
                $maintenance->update(['status' => 'completed']);
            }
        }
    }
}