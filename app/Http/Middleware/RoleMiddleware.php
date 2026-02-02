<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Vérifier si le compte est actif
        if (!$user->is_active) {
            Auth::logout();
            return redirect('/login')->with('error', 'Votre compte est en attente d\'activation.');
        }

        // Vérifier si le rôle de l'utilisateur est dans la liste des rôles autorisés
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        return abort(403, 'Accès non autorisé.');
    }
}