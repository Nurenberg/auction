<?php

declare(strict_types=1);

use App\Data\Doctrine\FixDefaultSchemaSubscriber;

return [
    'config' => [
        'doctrine' => [
            'dev_mode' => true,
            'cache_dir' => null,
            'proxy_dir' => __DIR__ . '/../../var/cache/' . getenv('PHP_IN') . '/doctrine/proxy',
            'subscribers' => [
                FixDefaultSchemaSubscriber::class
            ]
        ],
    ],
];
