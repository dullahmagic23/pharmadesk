<?php
return [
    'hosts' => [
        env('ELASTICSEARCH_HOST', 'http://127.0.0.1:9200'),
    ],
    'auth' => [
        'username' => env('ELASTICSEARCH_USER', 'elastic'),
        'password' => env('ELASTICSEARCH_PASS', ''),
    ],
];
