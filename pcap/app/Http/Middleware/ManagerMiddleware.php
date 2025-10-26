<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ManagerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        $allowedRoles = ['supervisor', 'manager', 'head', 'supermanager'];
        
        if ($user && in_array($user->role, $allowedRoles)) {
            return $next($request);
        }

        // Jeśli użytkownik nie jest zalogowany lub nie ma odpowiedniej roli
        return redirect('/login')->with('error', 'Nie masz dostępu do tej strony.');
    }
}
