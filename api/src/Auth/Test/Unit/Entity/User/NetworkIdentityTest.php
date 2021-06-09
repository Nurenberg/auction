<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\NetworkIdentity;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class NetworkIdentityTest extends TestCase
{
    public function testSuccess(): void
    {
        $name = 'google';
        $identity = 'google-1';
        $network = new NetworkIdentity($name, $identity);

        self::assertEquals($name, $network->getNetwork());
        self::assertEquals($identity, $network->getIdentity());
    }

    public function testEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NetworkIdentity('', 'google-1');
    }

    public function testEmptyIdentity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NetworkIdentity('google', '');
    }

    public function testEqual(): void
    {
        $name = 'google';
        $identity = 'google-1';
        $network = new NetworkIdentity($name, $identity);

        self::assertTrue($network->isEqualTo($network));
        self::assertFalse($network->isEqualTo(new NetworkIdentity($name, 'google-2')));
        self::assertFalse($network->isEqualTo(new NetworkIdentity('vk', 'vk-1')));
    }
}
