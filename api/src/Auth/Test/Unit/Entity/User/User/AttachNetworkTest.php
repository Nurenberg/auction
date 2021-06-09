<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

class AttachNetworkTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $network = new NetworkIdentity('vk', 'vk-1');
        $user->attachNetwork($network);

        $networks = $user->getNetworks();

        self::assertCount(1, $networks);
        self::assertEquals($network, $networks[0] ?? null);
    }

    public function testAlreadyAttached(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $network = new NetworkIdentity('vk', 'vk-1');
        $user->attachNetwork($network);

        self::expectErrorMessage('Network is already attached.');
        $user->attachNetwork($network);
    }
}
