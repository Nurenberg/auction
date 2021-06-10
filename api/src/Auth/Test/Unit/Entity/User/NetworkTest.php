<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Network;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class NetworkTest extends TestCase
{
    public function testSuccess(): void
    {
        $name = 'google';
        $identity = 'google-1';
        $network = new Network($name, $identity);

        self::assertEquals($name, $network->getName());
        self::assertEquals($identity, $network->getIdentity());
    }

    public function testEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Network('', 'google-1');
    }

    public function testEmptyIdentity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Network('google', '');
    }

    public function testEqual(): void
    {
        $name = 'google';
        $identity = 'google-1';
        $network = new Network($name, $identity);

        self::assertTrue($network->isEqualTo($network));
        self::assertFalse($network->isEqualTo(new Network($name, 'google-2')));
        self::assertFalse($network->isEqualTo(new Network('vk', 'vk-1')));
    }
}
