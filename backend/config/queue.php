<?php

return [

    'default' => env('QUEUE_CONNECTION', 'rabbitmq'),

    'connections' => [

        'rabbitmq' => [
            'driver' => 'rabbitmq',
            'queue' => env('RABBITMQ_QUEUE', 'laravel_queue'),
            'connection' => PhpAmqpLib\Connection\AMQPLazyConnection::class,

            'hosts' => [
                [
                    'host' => env('RABBITMQ_HOST', 'rabbitmq'),
                    'port' => env('RABBITMQ_PORT', 5672),
                    'user' => env('RABBITMQ_USER', 'admin'),
                    'password' => env('RABBITMQ_PASSWORD', 'secret'),
                    'vhost' => env('RABBITMQ_VHOST', '/'),
                ],
            ],

            'options' => [
                'ssl_options' => [
                    'cafile' => env('RABBITMQ_SSL_CAFILE', null),
                    'local_cert' => env('RABBITMQ_SSL_LOCALCERT', null),
                    'local_key' => env('RABBITMQ_SSL_LOCALKEY', null),
                    'verify_peer' => env('RABBITMQ_SSL_VERIFY_PEER', true),
                    'passphrase' => env('RABBITMQ_SSL_PASSPHRASE', null),
                ],
                'queue' => [
                    'job' => VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob::class,
                ],
            ],

            'exchange' => env('RABBITMQ_EXCHANGE', 'default'),
            'exchange_type' => env('RABBITMQ_EXCHANGE_TYPE', 'direct'),

            'queue_declare_bind' => env('RABBITMQ_QUEUE_DECLARE_BIND', true),
            'exchange_declare' => env('RABBITMQ_EXCHANGE_DECLARE', true),

            'queue_params' => [
                'passive'     => false,
                'durable'     => true,
                'exclusive'   => false,
                'auto_delete' => false,
                'arguments'   => null,
            ],

            // Настройки exchange
            'exchange_params' => [
                'passive'     => false,
                'durable'     => true,
                'auto_delete' => false,
                'internal'    => false,
                'arguments'   => null,
            ],

            'worker' => env('RABBITMQ_WORKER', 'default'),
        ],

    ],

    'batching' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'job_batches',
    ],

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'failed_jobs',
    ],

];
