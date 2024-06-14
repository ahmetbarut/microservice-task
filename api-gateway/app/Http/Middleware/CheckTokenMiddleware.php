<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_if(
            boolean: !$request->expectsJson(),
            code: 404,
            message: 'Not Found'
        );

        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Token not provided'
            ], 401);
        }

        $response = Http::acceptJson()
            ->withToken($request->bearerToken())
            ->get('http://security_service:8000/api/security/v1/users');

        if ($response->clientError()) {
            return response()->json([
                'message' => 'Unauthorized',
                'errors' => $response->json()
            ], $response->status());
        }

        $request->merge([
            'user_id' => $response->json('id')
        ]);

        return $next($request);
    }
}
