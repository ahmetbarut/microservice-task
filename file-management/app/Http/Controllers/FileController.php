<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileCreateRequest;
use App\Http\Requests\FileStoreRequest;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function storeFile(FileStoreRequest $request)
    {
        $requestFile = $request->file('file');

        $file = new File();
        $file->name = $request->name;
        $file->path = $requestFile->store('files');
        $file->mime_type = $requestFile->getMimeType();
        $file->size = $requestFile->getSize();
        $file->user_id = $request->user_id;
        $file->license_id = $request->license_id;
        $file->save();

        return response()->json($file, 201);
    }

    public function createFile(FileCreateRequest $request)
    {
        $extension = match ($request->mime_type) {
            'text/plain' => 'txt',
            'application/pdf' => 'pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/vnd.ms-powerpoint' => 'ppt',
            default => 'txt',
        };

        if (!FacadesFile::isDirectory(storage_path('files'))) {
            FacadesFile::makeDirectory(storage_path('files'));
        }

        $path = 'files/' . sha1(md5($request->name) . time()) . '.' . $extension;

        Storage::put($path, $request->content);

        $file = new File();
        $file->name = $request->name;
        $file->path = $path;
        $file->mime_type = FacadesFile::mimeType(Storage::path($path));
        $file->size = FacadesFile::size(Storage::path($path));
        $file->user_id = $request->user_id;
        $file->license_id = $request->license_id;
        $file->save();

        return response()->json($file, 201);
    }

    public function showFile(Request $request, $file)
    {
        $file = File::where('uuid', $file)->first();

        return response()->json($file);
    }

    public function downloadFile(Request $request, $file)
    {
        $file = File::where('uuid', $file)->first();

        return response()->download(Storage::path($file->path));
    }

    public function deleteFile(Request $request, $file)
    {
        $file = File::where('uuid', $file)->first();

        FacadesFile::delete(storage_path($file->path));

        $file->delete();

        return response()->json(null, 204);
    }

    public function quota(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'integer'],
        ]);

        $license = Http::acceptJson()->get('http://license_service:8000/api/v1/license', [
                'user_id' => $request->user_id,
            ])->object();

        $size = File::where('user_id', $request->user_id)->sum('size');

        return response()->json([
            'quota' => $license->data->quota,
            'used' => $size,
            'available' => $license->data->quota - $size,
        ]);
    }
}