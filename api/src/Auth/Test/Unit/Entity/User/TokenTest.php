<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Token;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class TokenTest extends TestCase
{
    public function testSuccess(): void
    {
        $value = Uuid::uuid4()->__toString();
        $expires = new DateTimeImmutable();

        $token = new Token(
            $value,
            $expires
        );

        $this->assertEquals($value, $token->getValue());
        $this->assertEquals($expires, $token->getExpires());
    }

    public function testCase(): void
    {
        $value = Uuid::uuid4()->__toString();

        $token = new Token(
            mb_strtoupper($value),
            new DateTimeImmutable()
        );

        $this->assertEquals($value, $token->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('12345', new DateTimeImmutable());
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('', new DateTimeImmutable());
    }
}
