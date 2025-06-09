<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Important : assurez-vous que ceci est importé
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles Les rôles autorisés pour cette route (ex: 'Administrateur', 'Superviseur')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Vérifier si un utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirige vers la page de connexion si non authentifié
        }

        $user = Auth::user();

        // 2. Vérifier si l'utilisateur connecté a un des rôles requis
        foreach ($roles as $role) {
            // CORRECTION ICI : Accéder à la propriété 'nom' de l'objet 'role'
            // Assurez-vous que $user->role n'est pas null avant d'accéder à 'nom'
            if ($user->role && $user->role->nom === $role) {
                return $next($request); // L'utilisateur a le rôle requis, autorise l'accès
            }
        }

        // 3. Si aucun rôle correspondant n'est trouvé après avoir vérifié tous les rôles passés, refuser l'accès
        abort(403, 'Accès non autorisé.'); // Affiche une erreur 403 Forbidden
    }
}
