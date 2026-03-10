<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginAdmin
{
    /**
     * Verificar que el usuario sea administrador.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->rol !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado. Se requiere rol de administrador.'
            ], 403);
        }

        return $next($request);
    }
}
