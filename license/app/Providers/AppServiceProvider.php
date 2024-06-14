<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind('rabbitmq', function (): AMQPStreamConnection {
            return new AMQPStreamConnection(
                config('services.rabbitmq.host'),
                config('services.rabbitmq.port'),
                config('services.rabbitmq.user'),
                config('services.rabbitmq.password'),
                config('services.rabbitmq.vhost')
            );
        });
    }
}
