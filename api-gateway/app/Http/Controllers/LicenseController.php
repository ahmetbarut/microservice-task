<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LicenseController extends Controller
{
    public function index(Request $request)
    {
        $response = Http::acceptJson()
            ->withToken(request()->bearerToken())
            ->get('http://license_service:8000/api/v1/license', [
                'user_id' => $request->user_id,
            ]);

        return $response->json();
    }
}
