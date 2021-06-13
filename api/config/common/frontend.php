<?php

declare(strict_types=1);

use App\Frontend\FrontendUrlGenerator;
use Psr\Container\ContainerInterface;

return [
    FrontendUrlGenerator::class => static function (ContainerInterface $container) {
        $frontendConfig = $container->get('config')['frontend'];

        return new FrontendUrlGenerator($frontendConfig['url']);
    },

    'config' => [
        'frontend' => [
            'url' => getenv('FRONTEND_URL')
        ]
    ],
];