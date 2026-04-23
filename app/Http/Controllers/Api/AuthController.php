<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use OpenApi\Attributes as OA;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class AuthController extends Controller
{
    protected function guard(): JWTGuard
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');
        return $guard;
    }

    #[OA\Post(
        path: '/api/auth/login',
        tags: ['Auth'],
        summary: 'Iniciar sesión y obtener token JWT',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'root@leadchain.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'root'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login exitoso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Login exitoso'),
                        new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'),
                        new OA\Property(property: 'token_type', type: 'string', example: 'bearer'),
                        new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
                        new OA\Property(property: 'user', type: 'object'),
                        new OA\Property(property: 'inicio', type: 'string', example: '/admin/inicio'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Credenciales inválidas'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function login(AuthRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = $this->guard()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas',
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    #[OA\Post(
        path: '/api/auth/logout',
        tags: ['Auth'],
        summary: 'Cerrar sesión',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Sesión cerrada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function logout()
    {
        $this->guard()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente',
        ]);
    }

    #[OA\Post(
        path: '/api/auth/refresh',
        tags: ['Auth'],
        summary: 'Refrescar token JWT',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Token renovado'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function refresh()
    {
        try {
            // Intentar refrescar el token (funciona incluso con tokens expirados)
            $newToken = $this->guard()->refresh();
            return $this->respondWithToken($newToken);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al refrescar token: ' . $e->getMessage(),
            ], 401);
        }
    }

    #[OA\Get(
        path: '/api/auth/me',
        tags: ['Auth'],
        summary: 'Obtener usuario autenticado',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Datos del usuario autenticado'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
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
            'inicio' => $this->getInicioByRole($user->rol),
        ]);
    }

    protected function respondWithToken($token)
    {
        // Intentar obtener el usuario normalmente
        $user = $this->guard()->user();

        // Si falla, intenta extraer del token (para tokens recién refrescados)
        if (!$user) {
            try {
                // Decodificar el nuevo token para obtener datos del usuario
                \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::setToken($token);
                $decoded = \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::getPayload();
                $userId = $decoded->get('sub'); // 'sub' es el user_id en JWT
                
                $user = \App\Models\User::find($userId);
                if (!$user) {
                    throw new \Exception('Usuario no encontrado');
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error obteniendo datos del usuario: ' . $e->getMessage(),
                ], 401);
            }
        }

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
            'inicio' => $this->getInicioByRole($user->rol),
        ]);
    }

    protected function getInicioByRole(string $rol): string
    {
        return match ($rol) {
            'admin' => '/admin/dashboard',
            'comercial' => '/comercial/dashboard',
            default => '/dashboard',
        };
    }
}
