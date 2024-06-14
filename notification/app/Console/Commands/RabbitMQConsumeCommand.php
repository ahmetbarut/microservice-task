<?php

namespace App\Console\Commands;

use App\Models\License;
use App\Models\Notification;
use App\Notifications\UserRegistered;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQConsumeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:consume {--queue=notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'RabbitMQ consume message';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = app('rabbitmq');

        /**
         * @var \PhpAmqpLib\Channel\AMQPChannel $channel
         */
        $channel = $connection->channel();
        $queue = $this->option('queue');

        $channel->queue_declare($queue, false, false, false, false);

        $callback = function ($msg) {
            if (!json_validate($msg->body)) {
                throw new \Exception('Invalid JSON');
            }

            $data = json_decode($msg->body, true);

            $type = Arr::get($data, 'type');

            $notification = new Notification(attributes: [
                'notification_type' => $type,
                ...$data['data'],
            ]);
            $notification->save();
        };

        $channel->basic_consume($queue, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
