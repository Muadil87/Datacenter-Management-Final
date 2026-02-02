<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource; 

class GuestController extends Controller
{
    public function index() {
        // Récupérer toutes les ressources
        $resources = Resource::all();
        
        // Les envoyer à la vue
        return view('guest.home', compact('resources')); 
    }

    public function rules() {
        return view('guest.rules');
    }
}