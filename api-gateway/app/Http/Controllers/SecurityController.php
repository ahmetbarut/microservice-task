<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SecurityController extends Controller
{
    public function index()
    {
        $response = Http::acceptJson()
            ->withToken(request()->bearerToken())
            ->get('http://security_service:8000/api/security/v1/users');

        if ($response->clientError()) {
            return response()->json([
                'message' => 'Unauthorized',
                'errors' => $response->json()
            ], $response->status());
        }

        return response()->json(
            $response->json()
        );
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $response = Http::acceptJson()
            ->post('http://security_service:8000/api/security/v1/users', $data);

        if ($response->clientError()) {
            return response()->json([
                'message' => 'Invalid data',
                'errors' => $response->json()
            ], $response->status());
        }

        return response()->json([
            'data' => $response->json()
        ], $response->status());
    }

    public function login(Request $request)
    {
        $data = $request->all();

        $response = Http::acceptJson()
            ->post('http://security_service:8000/api/security/v1/users/login', $data);

        if ($response->clientError()) {
            return response()->json([
                'message' => 'Unauthorized',
                'errors' => $response->json()
            ], $response->status());
        }

        return response()->json([
            'data' => $response->json()
        ]);
    }
}
