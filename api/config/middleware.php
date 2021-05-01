<?php

declare(strict_types=1);

use DI\Container;
use Slim\App;

return static function (App $app, Container $container): void {
    /** @psalm-var array{debug:bool} */
    $config = $container->get('config');

    $app->addErrorMiddleware($config['debug'], true, true);
};
