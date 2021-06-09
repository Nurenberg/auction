<?php

declare(strict_types=1);

namespace Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Entity\User\User;
use App\Auth\Service\PasswordHasher;
use App\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ChangePasswordTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $hash = 'new-hash';

        $hasher = $this->createHasher(true, $hash);

        $user->changePassword(
            'old-password',
            'new-password',
            $hasher
        );

        self::assertEquals($hash, $user->getPasswordHash());
    }

    public function testWrongCurrent(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $hash = 'new-hash';

        $hasher = $this->createHasher(false, $hash);

        $this->expectExceptionMessage('Incorrect current password');
        $user->changePassword(
            'wrong-old-password',
            'new-password',
            $hasher
        );

        self::assertEquals($hash, $user->getPasswordHash());
    }

    public function testByNetwork(): void
    {
        $user = (new UserBuilder())
            ->viaNetwork()
            ->build();

        $hash = 'new-hash';
        $hasher = $this->createHasher(false, $hash);

        $this->expectExceptionMessage('User does not have an old password.');
        $user->changePassword(
            'any-old-password',
            'new-password',
            $hasher
        );

    }

    private function createHasher(bool $validate, string $hash): Object
    {
        $hasher = $this->createStub(PasswordHasher::class);
        $hasher->method('validate')->willReturn($validate);
        $hasher->method('hash')->willReturn($hash);

        return $hasher;
    }
}