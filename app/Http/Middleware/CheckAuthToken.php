<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facade\Http;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if(!$token) {
            return response()->json([''message => 'Token no proporcionado'], 401);
        }

        // URL del microservicio de autenticacion
        $authUrl = rtrim(env('AUTH_SERVICE_URL', 'http://127.0.0.1:8000'), '/');

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer' . $token,
        ])->get("{$authUrl}/api/validate-token");

        if ($response->failed() || $response->status() !== 200) {
            return response()->json(['message' => 'Token invalido o servicio disponible'], 401);
        }
        
        $data = $response->json();

        if (!isset($data['valid'] !== true) {
            return response()->json(['message' => 'Token invalido expirado'], 401);
        }
        $request->attributes->set('auth_user', $data['user']);
        
        return $next($request);
    }
}
