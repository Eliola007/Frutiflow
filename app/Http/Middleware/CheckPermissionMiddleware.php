<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            abort(401, 'No autenticado');
        }

        $user = Auth::user();
        
        // Verificar si el usuario tiene el permiso
        if (!$user->can($permission)) {
            abort(403, "No tienes permiso para realizar esta acción. Permiso requerido: {$permission}");
        }

        // Si es Socio Comercial, verificar restricciones adicionales
        if ($user->hasRole('Socio Comercial')) {
            $this->verificarRestriccionesSocio($request, $user);
        }

        return $next($request);
    }

    /**
     * Verificar restricciones específicas para Socios Comerciales
     */
    private function verificarRestriccionesSocio(Request $request, $user)
    {
        // Si está accediendo a productos, verificar que tenga acceso a ese producto específico
        if ($request->route('producto')) {
            $productoId = $request->route('producto');
            $tieneAcceso = $user->productosAsignados()->where('productos.id', $productoId)->exists();
            
            if (!$tieneAcceso) {
                abort(403, 'No tienes acceso a este producto específico');
            }
        }

        // Similar lógica para reportes de productos específicos
        if ($request->has('producto_id')) {
            $productoId = $request->get('producto_id');
            $tieneAcceso = $user->productosAsignados()->where('productos.id', $productoId)->exists();
            
            if (!$tieneAcceso) {
                abort(403, 'No tienes acceso a los datos de este producto');
            }
        }
    }
}
