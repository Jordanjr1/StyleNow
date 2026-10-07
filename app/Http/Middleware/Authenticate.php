<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return $this->unauthenticated($request, $guards);
        }

        // Verificar si la sesión es válida
        if (Auth::viaRemember() || Auth::user()) {
            // Regenerar el ID de sesión periódicamente para mayor seguridad
            // Usamos una marca de tiempo para regenerar cada 30 minutos
            $lastRegeneration = $request->session()->get('last_session_regeneration', 0);
            $currentTime = time();
            
            if (($currentTime - $lastRegeneration) > 1800) { // 1800 segundos = 30 minutos
                $request->session()->regenerate();
                $request->session()->put('last_session_regeneration', $currentTime);
            }
        }

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        if (!$request->expectsJson()) {
            // Redirigir al login con un mensaje
            session()->flash('error', 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.');
            return route('login');
        }
        
        return null;
    }
}