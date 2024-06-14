<?php

use App\Models\License;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use PhpAmqpLib\Message\AMQPMessage;

Schedule::call(function () {
    $connection = app('rabbitmq');
    $channel = $connection->channel();

    License::where('expired_at', '<', now())
        ->where('status', 'active')
        ->each(function ($license) use ($channel) {
            $channel->queue_declare('notification', false, true, false, false);
            $channel->basic_publish(new AMQPMessage(json_encode([
                'notification_type' => 'license_expired',
                'data' => [
                    'title' => 'License Expired',
                    'message' => 'Your license has been expired',
                    'user_id' => $license->user_id,
                ]
            ])), '', 'notification');

            $license->update(['status' => 'expired']);
        });

    $channel->close();
    $connection->close();
})->everyMinute();
