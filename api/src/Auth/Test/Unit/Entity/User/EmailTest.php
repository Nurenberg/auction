<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Entity\User\Email
 */
class EmailTest extends TestCase
{
    public function testSuccess(): void
    {
        $value = 'email@goo.com';
        $email = new Email($value);

        $this->assertEquals($value, $email->getValue());
    }

    public function testCase(): void
    {
        $value = 'Email@goo.com';
        $email = new Email($value);

        $this->assertEquals(mb_strtolower($value), $email->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('not email');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('');
    }
}
