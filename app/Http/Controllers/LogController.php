<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index() {
        // Récupère les 50 derniers logs avec eager loading de l'utilisateur
        $logs = Log::with('user')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();
        return view('stats.logs_list', compact('logs'));
    }
}