<?php

namespace App\Http\Controllers;

use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LicenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'integer']
        ]);

        $license = License::where('user_id', $request->user_id)->latest()->default()->first();

        return response()->json([
            'data' => $license
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(License $license)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(License $license)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, License $license)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(License $license)
    {
        //
    }
}
