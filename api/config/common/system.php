<?php

declare(strict_types=1);

return [
    'config' => [
        'debug' => (bool)getenv('APP_DEBUG'),
        'console' => [
            'commands' => require __DIR__ . '/../commands.php'
        ],
    ]
];
