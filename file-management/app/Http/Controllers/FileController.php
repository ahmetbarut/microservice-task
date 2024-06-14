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
use PhpAmqpLib\Message\AMQPMessage;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $response = Http::acceptJson()
            ->withToken($request->bearerToken())
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

        $this->sendNotification(json_encode([
            'notification_type' => 'file_uploaded',
            'data' => [
                'title' => 'File Uploaded',
                'message' => 'File has been uploaded',
                'user_id' => $request->user_id,
            ]
        ]));

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

        $this->sendNotification(json_encode([
            'notification_type' => 'file_created',
            'data' => [
                'title' => 'File Created',
                'message' => 'File has been created',
                'user_id' => $request->user_id,
            ]
        ]));

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

        $this->sendNotification(json_encode([
            'notification_type' => 'file_deleted',
            'data' => [
                'title' => 'File Deleted',
                'message' => 'File has been deleted',
                'user_id' => $request->user_id,
            ]
        ]));

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

    public function updateFile(FileStoreRequest $request)
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

        $this->sendNotification(json_encode([
            'notification_type' => 'file_updated',
            'data' => [
                'title' => 'File Updated',
                'message' => 'File has been updated',
                'user_id' => $request->user_id,
            ]
        ]));

        return response()->json($file, 201);
    }

    protected function sendNotification(string $msg)
    {
        $connection = app('rabbitmq');
        $channel = $connection->channel();
        $channel->queue_declare('notification', false, true, false, false);

        $msg = new AMQPMessage($msg);
        $channel->basic_publish($msg, '', 'notification');

        $channel->close();
        $connection->close();
    }
}
