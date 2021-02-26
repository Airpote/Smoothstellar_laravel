<?php 
return [ 
    'client_id' => env('PAYPAL_CLIENT_ID',''),
    'secret' => env('PAYPAL_SECRET',''),
    'settings' => array(
        'mode' => env('PAYPAL_MODE','sandbox'),
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => true,
        'log.FileName' => storage_path() . '/logs/paypal.log',
        'log.LogLevel' => 'ERROR'
    ),
    'stellar_private_key' => env('STELLAR_PRIVATE_KEY',''),
    'stellar_public_key' => env('STELLAR_PUBLIC_KEY',''),
    'admin_private_key' => env('ISSURE_SECRET',''),
    'admin_public_key' => env('ISSURE_ID',''),
];