<?php

namespace App\Observers;

use App\Models\Resource;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class ResourceObserver
{
    /**
     * Handle the Resource "created" event.
     */
    public function created(Resource $resource): void
    {
        Log::create([
            'user_id' => Auth::id() ?? 1,
            'action'  => 'Création Ressource',
            'details' => "Nouvelle ressource ajoutée : {$resource->name}",
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Handle the Resource "updated" event.
     */
    public function updated(Resource $resource): void
    {
        Log::create([
            'user_id' => Auth::id() ?? 1,
            'action'  => 'Mise à jour Ressource',
            'details' => "Ressource modifiée : {$resource->name}",
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Handle the Resource "deleted" event.
     */
    public function deleted(Resource $resource): void
    {
        Log::create([
            'user_id' => Auth::id() ?? 1,
            'action'  => 'Suppression Ressource',
            'details' => "Ressource supprimée : {$resource->name}",
            'ip_address' => request()->ip()
        ]);
    }
}
