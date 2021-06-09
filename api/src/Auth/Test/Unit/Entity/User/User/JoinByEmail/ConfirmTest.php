<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\JoinByEmail;

use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $token = $this->createToken();

        $user = (new UserBuilder())
            ->withJoinConfirmToken($token)
            ->build();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getJoinConfirmToken());
    }

    private function createToken(): Token
    {
        return new Token(
            Uuid::uuid4()->__toString(),
            new DateTimeImmutable('+1 day')
        );
    }

    public function testWrong(): void
    {
        $token = $this->createToken();

        $user = (new UserBuilder())
            ->withJoinConfirmToken($token)
            ->build();

        $this->expectErrorMessage('Token is invalid.');

        $user->confirmJoin(
            Uuid::uuid4()->__toString(),
            $token->getExpires()->modify('-1 day')
        );
    }

    public function testExpired(): void
    {
        $token = $this->createToken();

        $user = (new UserBuilder())
            ->withJoinConfirmToken($token)
            ->build();

        $this->expectErrorMessage('Token is expired.');

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('+1 day')
        );
    }

    public function testAlready()
    {
        $token = $this->createToken();

        $user = (new UserBuilder())
            ->withJoinConfirmToken($token)
            ->active()
            ->build();

        $this->expectErrorMessage('Confirmation is not required.');

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );
    }
}
