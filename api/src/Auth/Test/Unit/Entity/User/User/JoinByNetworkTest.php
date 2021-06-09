<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class JoinByNetworkTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = Id::generate();
        $date = new DateTimeImmutable();
        $email = new Email('test@mail.com');
        $network = new NetworkIdentity('google', 'google-1');

        $user = User::joinByNetwork(
            $id,
            $date,
            $email,
            $network
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($email, $user->getEmail());

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertCount(1, $user->getNetworks());
        self::assertSame($network, $user->getNetworks()[0] ?? null);
    }
}
