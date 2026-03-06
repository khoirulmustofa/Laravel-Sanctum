<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register', 'email/*', 'forgot-password', 'reset-password'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        env('FRONTEND_MOBILE', 'http://localhost:8100'),
        'http://localhost:8100',
        'http://127.0.0.1:8100',
        'https://service.nfbs-bogor.sch.id',
        'https://e6c4-180-243-122-114.ngrok-free.app',

        // --- TAMBAHKAN BARIS DI BAWAH INI ---
        'http://localhost',
        'https://localhost',       // <--- PENTING: Origin default Capacitor Android
        'capacitor://localhost',   // <--- Jaga-jaga jika pakai skema lama/iOS
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
