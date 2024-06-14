<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpFoundation\Response;

class QuotaCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = Http::acceptJson()
            ->get('http://file_management_service:8000/api/fms/v1/files/quota', [
                'user_id' => $request->user_id,
            ]);

        if ($response->status() !== 200) {
            return response()->json([
                'message' => 'Failed to get quota',
            ], 500);
        }

        $quota = $response->json();

        if ($quota['available'] < 0) {
            /**
             * @var \PhpAmqpLib\Connection\AMQPStreamConnection
             */
            $connection = app('rabbitmq');
            $channel = $connection->channel();
            $channel->queue_declare('notification', false, true, false, false);
            $msg = new AMQPMessage(json_encode([
                'type' => 'quota_exceeded',
                'data' => [
                    'user_id' => $request->user_id,
                    'content' => 'Quota exceeded for user ' . $request->user_id,
                ],
            ]));

            $channel->basic_publish($msg, '', 'notification');

            return response()->json([
                'message' => 'Quota exceeded',
            ], 400);
        }

        return $next($request);
    }
}
