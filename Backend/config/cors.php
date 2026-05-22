<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure CORS settings for your application. This
    | determines which cross-origin requests are allowed.
    |
    */

    'paths' => ['api/*', '/api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'localhost:5173',      // Vite dev server padrão
        'localhost:3000',      // Alternativa
        '127.0.0.1:5173',
        '127.0.0.1:3000',
        'http://localhost:5173',
        'http://localhost:3000',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:3000',
    ],

    'allowed_origins_patterns' => [
        '#^http://localhost:\d+$#',
        '#^http://127\.0\.0\.1:\d+$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
