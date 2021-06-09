<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Id;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class IdTest extends TestCase
{
    public function testSuccess(): void
    {
        $value = Uuid::uuid4()->__toString();
        $id = new Id($value);

        $this->assertEquals($value, $id->getValue());
    }

    public function testCase()
    {
        $value = Uuid::uuid4()->__toString();
        $id = new Id(mb_strtoupper($value));

        $this->assertEquals($value, $id->getValue());
    }

    public function testGenerate(): void
    {
        $this->assertNotEmpty(Id::generate(), 'Id is empty');
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Id('12345');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Id('');
    }
}
