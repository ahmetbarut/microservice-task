<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FileManagementController extends Controller
{
    public function createFile(Request $request)
    {
        $response = Http::acceptJson()
            ->post('http://file_management_service:8000/api/fms/v1/files/create', $request->all());

        return response()->json($response->json(), $response->status());
    }

    public function storeFile(Request $request)
    {
        $response = Http::acceptJson()
            ->attach('file', $request->file('file')->getContent(), $request->file('file')->getClientOriginalName())
            ->post('http://file_management_service:8000/api/fms/v1/files/store', $request->except(['file']));

        return response()->json($response->json(), $response->status());
    }

    public function showFile(Request $request, $file)
    {
        $response = Http::acceptJson()
            ->get("http://file_management_service:8000/api/fms/v1/files/show/{$file}");

        return response()->json($response->json(), $response->status());
    }

    public function downloadFile(Request $request, $file)
    {
        $response = Http::acceptJson()
            ->get("http://file_management_service:8000/api/fms/v1/files/download/{$file}");

        Storage::put($file, $response->body());

        return response()->download(Storage::path($file))->deleteFileAfterSend(true);
    }

    public function quota(Request $request)
    {
        $response = Http::acceptJson()
            ->get('http://file_management_service:8000/api/fms/v1/files/quota', $request->all());

        return response()->json($response->json(), $response->status());
    }

    public function updateFile(Request $request, $file)
    {
        $response = Http::acceptJson()
            ->post("http://file_management_service:8000/api/fms/v1/files/update/{$file}", $request->all());

        return response()->json($response->json(), $response->status());
    }

    public function deleteFile(Request $request, $file)
    {
        $response = Http::acceptJson()
            ->delete("http://file_management_service:8000/api/fms/v1/files/delete/{$file}");

        return response()->json($response->json(), $response->status());
    }
}
