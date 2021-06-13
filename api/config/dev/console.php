<?php

declare(strict_types=1);

use App\Console\FixturesLoadCommand;
use App\Console\MailerCheckCommand;
use Doctrine\Migrations;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;
use Psr\Container\ContainerInterface;

return [
    FixturesLoadCommand::class => static function (ContainerInterface $container) {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{fixture_paths[]} $config
         */
        $config = $container->get('config')['console'];

        $em = $container->get(EntityManagerInterface::class);

        return new FixturesLoadCommand(
            $em,
            $config['fixture_paths'],
        );
    },
    'config' => [
        'console' => [
            'commands' => [
                FixturesLoadCommand::class,
                MailerCheckCommand::class,
                ValidateSchemaCommand::class,
                DropCommand::class,
                Migrations\Tools\Console\Command\ExecuteCommand::class,
                Migrations\Tools\Console\Command\MigrateCommand::class,
                Migrations\Tools\Console\Command\LatestCommand::class,
                Migrations\Tools\Console\Command\ListCommand::class,
                Migrations\Tools\Console\Command\StatusCommand::class,
                Migrations\Tools\Console\Command\UpToDateCommand::class,
                Migrations\Tools\Console\Command\GenerateCommand::class,
                Migrations\Tools\Console\Command\DiffCommand::class,
            ],
            'fixture_paths' => [
                __DIR__ . '/../../src/Auth/Fixture'
            ],
        ],
    ],
];
