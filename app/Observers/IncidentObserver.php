<?php

namespace App\Observers;

use App\Models\Incident;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class IncidentObserver
{
    // Déclenché quand un incident est CRÉÉ
    public function created(Incident $incident): void
    {
        $resourceName = $incident->resource ? $incident->resource->name : 'Unknown';
        $userName = $incident->user ? $incident->user->name : 'Unknown';
        
        // Set resource to maintenance when incident is created
        if ($incident->resource) {
            $incident->resource->update(['state' => 'maintenance']);
        }
        
        Log::create([
            'user_id' => $incident->user_id,
            'action'  => 'Incident Créé',
            'details' => "{$userName} a signalé un incident : {$incident->title} sur {$resourceName}",
            'ip_address' => request()->ip()
        ]);
    }

    // Déclenché quand un incident est MODIFIÉ (ex: résolu)
    public function updated(Incident $incident): void
    {
        // On vérifie si le statut a changé
        if ($incident->isDirty('status')) {
            $oldStatus = $incident->getOriginal('status');
            $userName = Auth::user() ? Auth::user()->name : 'Admin';
            $resourceName = $incident->resource ? $incident->resource->name : 'Unknown';
            
            // Si l'incident passe à "resolu" (resolved in French), marquer la ressource comme disponible
            if ($oldStatus !== 'resolu' && $incident->status === 'resolu') {
                // Force reload the resource relationship
                $incident->load('resource');
                
                if ($incident->resource) {
                    \Log::info('Updating resource to available', [
                        'incident_id' => $incident->id,
                        'resource_id' => $incident->resource->id,
                        'resource_name' => $incident->resource->name,
                        'old_state' => $incident->resource->state
                    ]);
                    
                    $incident->resource->update(['state' => 'available']);
                    
                    \Log::info('Resource updated to available', [
                        'resource_id' => $incident->resource->id,
                        'new_state' => $incident->resource->fresh()->state
                    ]);
                }
            }
            
            Log::create([
                'user_id' => Auth::id() ?? 1,
                'action'  => 'Statut Incident Modifié',
                'details' => "{$userName} a changé le statut de l'incident #{$incident->id} ({$resourceName}) de '{$oldStatus}' à '{$incident->status}'",
                'ip_address' => request()->ip()
            ]);
        }
    }
}