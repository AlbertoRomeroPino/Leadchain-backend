<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class AuthController extends Controller
{
    /**
     * Obtener guard JWT tipado
     */
    protected function guard(): JWTGuard
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');
        return $guard;
    }

    /**
     * Login de usuario con JWT
     */
    public function login(AuthRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = $this->guard()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Cerrar sesión (invalidar token)
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ]);
    }

    /**
     * Refrescar token JWT
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Obtener usuario autenticado
     */
    public function me()
    {
        $user = $this->guard()->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'apellidos' => $user->apellidos,
                'email' => $user->email,
                'rol' => $user->rol,
                'id_zona' => $user->id_zona,
            ],
            'dashboard' => $this->getDashboardByRole($user->rol)
        ]);
    }

    /**
     * Respuesta estructurada con token
     */
    protected function respondWithToken($token)
    {
        $user = $this->guard()->user();

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'apellidos' => $user->apellidos,
                'email' => $user->email,
                'rol' => $user->rol,
                'id_zona' => $user->id_zona,
            ],
            'dashboard' => $this->getDashboardByRole($user->rol)
        ]);
    }

    /**
     * Determinar dashboard según rol
     */
    protected function getDashboardByRole(string $rol): string
    {
        return match ($rol) {
            'admin' => '/admin/dashboard',
            'comercial' => '/comercial/dashboard',
            default => '/dashboard',
        };
    }
}
