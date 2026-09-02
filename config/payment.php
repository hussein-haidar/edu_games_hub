<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver Pembayaran
    |--------------------------------------------------------------------------
    | mock     = simulasi (default, untuk pengembangan)
    | midtrans = Midtrans Snap (isi server_key & client_key pada .env / services.php)
    |
    */

    'driver' => env('PAYMENT_DRIVER', 'mock'),

    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY', ''),
        'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    ],

];
