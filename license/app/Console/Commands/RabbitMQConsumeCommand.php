<?php

namespace App\Console\Commands;

use App\Models\License;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQConsumeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:consume {--queue=user_register}';

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
            Log::info('Received message', $data);
            if (!Arr::has($data, 'data.user_id')) {
                throw new \Exception('Missing user_id');
            }

            $license = new License(attributes: Arr::only($data['data'], [
                'name',
                'daily_file_limit',
                'max_storage',
                'quota',
                'user_id',
                'status',
                'license_type',
                'expires_at',
            ]));

            $license->save();
        };

        $channel->basic_consume($queue, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
