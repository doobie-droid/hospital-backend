<?php

return [
    'default' => 'test',
    'providers' => [
        'paystack' => [
            'public_key' => env('PAYSTACK_PUBLIC_KEY'),
            'secret_key' => env('PAYSTACK_SECRET_KEY'),
            'allowed_ips' => env('PAYSTACK_ALLOWED_IPS'),
        ],
        'flutterwave' => [
            'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
            'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
            'webhook_secret' => env('FLUTTERWAVE_WEBHOOK_HASH'),
        ],
    ],
];
