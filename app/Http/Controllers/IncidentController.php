<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Log;
use App\Models\Resource;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    // 1. Afficher la liste des incident
    public function index() {
        $incidents = Incident::with('user', 'resource')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('stats.incidents_list', compact('incidents'));
    }

    // 2. Afficher le formulaire de création
    public function create() {
        // Admins ne peuvent pas signaler d'incidents
        if (auth()->user()->role === 'admin') {
            return redirect()->route('home')->with('error', 'Les administrateurs ne peuvent pas signaler d\'incidents.');
        }

        // Manager vers sa propre interface
        if (auth()->user()->role === 'manager') {
            return redirect()->route('manager.incidents.report');
        }
        
        // Internal - afficher la vieille vue pour compatibilité
        $resources = Resource::all();
        return view('stats.incidents_create', compact('resources'));
    }

    // 3. Enregistrer l'incident dans la base de données
    public function store(Request $request) {
        // Admins ne peuvent pas signaler d'incidents
        if (auth()->user()->role === 'admin') {
            return redirect()->route('home')->with('error', 'Les administrateurs ne peuvent pas signaler d\'incidents.');
        }
        
        // Validation des données
        $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        // Sauvegarde de l'incident
        $incident = Incident::create([
            'user_id' => auth()->id(),
            'resource_id' => $request->resource_id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'ouvert',
        ]);

        // Note: Logging is handled by IncidentObserver to avoid duplicates
        
        // Notifier tous les administrateurs
        $admins = User::where('role', 'admin')->pluck('id')->toArray();
        Notification::notifyMultiple(
            $admins,
            '🔴 Nouvel Incident Signalé',
            auth()->user()->name . ' a signalé un incident: ' . $request->title,
            'incident_reported',
            $incident->id,
            'Incident'
        );

        // Notifier tous les managers si l'incident est signalé par un utilisateur interne
        if (auth()->user()->role === 'internal') {
            $managers = User::where('role', 'manager')->pluck('id')->toArray();
            if (!empty($managers)) {
                Notification::notifyMultiple(
                    $managers,
                    '🔴 Nouvel Incident Signalé',
                    auth()->user()->name . ' a signalé un incident: ' . $request->title,
                    'incident_reported',
                    $incident->id,
                    'Incident'
                );
            }
        }

        // Redirection vers la liste avec un message de succès
        return redirect()->route('incidents.create')->with('success', 'Incident signalé avec succès !');
    }

    // 4. Changer le statut de l'incident et mettre à jour l'état de la ressource
    public function updateStatus(Request $request, Incident $incident) {
        $request->validate([
            'status' => 'required|in:ouvert,en_cours,resolu'
        ]);

        $oldStatus = $incident->status;
        $incident->update(['status' => $request->status]);

        // Si l'incident est marqué "En cours", passer la ressource en "maintenance"
        if ($request->status == 'en_cours' && $incident->resource) {
            $incident->resource->update(['state' => 'maintenance']);
            
            // Notifier l'utilisateur qui a signalé
            Notification::notify(
                $incident->user_id,
                '🔧 Incident en Cours de Traitement',
                'Votre incident sur ' . $incident->resource->name . ' est maintenant en cours de traitement.',
                'incident_in_progress',
                $incident->id,
                'Incident'
            );
        }

        // Si l'incident est résolu, notifier l'utilisateur
        if ($request->status == 'resolu') {
            Notification::notify(
                $incident->user_id,
                '✅ Incident Résolu',
                'Votre incident sur ' . $incident->resource->name . ' a été résolu.',
                'incident_resolved',
                $incident->id,
                'Incident'
            );

            // Remettre la ressource en état "available"
            if ($incident->resource) {
                $incident->resource->update(['state' => 'available']);
            }
        }

        // Note: Logging is handled by IncidentObserver to avoid duplicates

        return redirect()->back()->with('success', 'Statut de l\'incident mis à jour !');
    }

    // 5. Marquer un incident comme résolu (raccourci)
    public function resolve($id) {
        $incident = Incident::findOrFail($id);
        
        return $this->updateStatus(
            new Request(['status' => 'resolu']),
            $incident
        );
    }
}