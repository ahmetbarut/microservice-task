<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpAmqpLib\Message\AMQPMessage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            User::all()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        /**
         * @var \PhpAmqpLib\Connection\AMQPStreamConnection $connection
         */
        $connection = app('rabbitmq');
        $channel = $connection->channel();

        $channel->queue_declare('user_register', false, false, false, false);
        $channel->basic_publish(new AMQPMessage(json_encode([
            'type' => 'user_register',
            'data' => [
                'user_id' => $user->id,
                'expires_at' => now()->addMonth(),
                'quota' => 1024 * 200, // 200 MB
                'max_storage' => 100,
                'daily_file_limit' => 5,
                'license_type' => 'Trial',
                'status' => 'Active'
            ],
        ])), '', 'user_register');

        $channel->close();
        $connection->close();

        return response()->json([
            'access_token' => $user->createToken('authToken')->accessToken,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || Hash::check($credentials['password'], $user->password) === false) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }


        return response()->json([
            'access_token' => $user->createToken('authToken')->accessToken,
        ]);
    }
}
