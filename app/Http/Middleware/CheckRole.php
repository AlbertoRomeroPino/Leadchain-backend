<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Verificar que el usuario tenga uno de los roles permitidos.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Roles permitidos (puede ser uno o varios)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }

        if (!in_array($user->rol, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para acceder a este recurso',
                'required_roles' => $roles,
                'your_role' => $user->rol
            ], 403);
        }

        return $next($request);
    }
}
