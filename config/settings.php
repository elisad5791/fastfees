<?php
return [
    'database' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => 3309,
        'database' => 'news',
        'username' => 'root',
        'password' => 'rootpassword',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],
    'redis' => [
        'host' => 'localhost',
        'port' => 6379,
        'password' => 'redispassword'
    ]
];