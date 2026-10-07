<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Si ya está autenticado, redirigir al dashboard según su rol
                $user = Auth::user();
                
                switch ($user->usr_rolId) {
                    case 1:
                        return redirect()->route('admin.dashboard');
                    case 2:
                        return redirect()->route('empleado.dashboard');
                    case 3:
                        return redirect()->route('cliente.dashboard');
                    default:
                        return redirect('/');
                }
            }
        }

        return $next($request);
    }
}