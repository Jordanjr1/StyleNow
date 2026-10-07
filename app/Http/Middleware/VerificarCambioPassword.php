<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarCambioPassword
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $usuario = Auth::user();
            
            // Si requiere cambio de contraseña y no está en la página de cambio
            if ($usuario->requiere_reset == 1 && !$request->is('cambio-contrasena*')) {
                return redirect()->route('cambio.contrasena');
            }
            
            // Si NO requiere cambio y está en la página de cambio, redirigir
            if ($usuario->requiere_reset == 0 && $request->is('cambio-contrasena*')) {
                return redirect($this->getRedirectUrlByRole($usuario->usr_rolId));
            }
        }
        
        return $next($request);
    }
    
    private function getRedirectUrlByRole($rolId)
    {
        switch ($rolId) {
            case 1: return '/admin/dashboard';
            case 2: return '/empleado/dashboard';
            case 3: return '/cliente/dashboard';
            default: return '/';
        }
    }
}