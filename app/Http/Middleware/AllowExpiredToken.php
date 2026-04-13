<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;

class AllowExpiredToken
{
    /**
     * Middleware para permitir tokens expirados
     * Usado especialmente para el endpoint de refresh
     * 
     * Valida la firma del token pero permite la expiración
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado',
            ], 401);
        }

        try {
            // Parsear el token sin validar expiración
            JWTAuth::setToken($token);
            $decoded = JWTAuth::parseToken();
            
            // Si llegamos aquí, el token tiene firma válida
            // Ahora intenta obtener el usuario
            try {
                $user = JWTAuth::authenticate();
                // Token válido y no expirado
                return $next($request);
            } catch (TokenExpiredException $e) {
                // Token expirado pero firma válida - PERMITIR para refresh
                // No hacer nada, simplemente permitir que continúe
                return $next($request);
            }

        } catch (\Exception $e) {
            // Cualquier otro error = token inválido
            return response()->json([
                'success' => false,
                'message' => 'Token inválido: ' . $e->getMessage(),
            ], 401);
        }
    }
}
