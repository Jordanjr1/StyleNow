<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Si no hay usuario autenticado, redirigir al login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder.');
        }
        
        $user = Auth::user();
        
        // Verificar si el usuario tiene uno de los roles permitidos
        if (!in_array($user->usr_rolId, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
        
        // Ejecutar la petición
        $response = $next($request);
        
        // IMPORTANTE: Prevenir caché del navegador
        return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', '0');
    }
}