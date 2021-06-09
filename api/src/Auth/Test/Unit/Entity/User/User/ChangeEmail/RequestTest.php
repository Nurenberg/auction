<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\ChangeEmail;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;


/**
 * @covers User
 */
class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $old = new Email('old-email@app.test');
        $user = (new UserBuilder())
            ->withEmail($old)
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $new = new Email('new-email@app.test');
        $user->requestEmailChanging($token, $now, $new);

        self::assertEquals($token, $user->getNewEmailToken());
        self::assertEquals($old, $user->getEmail());
        self::assertEquals($new, $user->getNewEmail());
    }

    public function testSame(): void
    {
        $old = new Email('old-email@app.test');
        $user = (new UserBuilder())
            ->withEmail($old)
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $this->expectExceptionMessage('Email is already same.');
        $user->requestEmailChanging($token, $now, $old);
    }

    public function testAlready(): void
    {
        $old = new Email('old-email@app.test');
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, $old);

        $this->expectExceptionMessage('Changing is already requested.');
        $user->requestEmailChanging($token, $now, $old);
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));
        $user->requestEmailChanging($token, $now, new Email('temp-email@app.test'));

        $newDate = $now->modify('+2 hours');
        $newToken = $this->createToken($newDate->modify('+1 hour'));
        $newEmail = new Email('new-email@app.test');
        $user->requestEmailChanging($newToken, $newDate, $newEmail);

        self::assertEquals($newToken, $user->getNewEmailToken());
        self::assertEquals($newEmail, $user->getNewEmail());
    }

    public function testNotActive(): void
    {
        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));
        $user = (new UserBuilder())->build();

        $this->expectExceptionMessage('User is not active.');
        $user->requestEmailChanging($token, $now, new Email('new-email@app.test'));
    }

    private function createToken(DateTimeImmutable $date): Token
    {
        return new Token(Uuid::uuid4()->__toString(), $date);
    }
}