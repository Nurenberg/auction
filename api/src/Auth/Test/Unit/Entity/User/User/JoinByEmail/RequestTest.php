<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\JoinByEmail;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Service\Tokenizer;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = Id::generate();
        $date = new DateTimeImmutable();
        $email = new Email('email@goo.com');
        $hash = 'hash';

        $tokenizer = new Tokenizer(new DateInterval('PT1H'));
        $token = $tokenizer->generate(new DateTimeImmutable());

        $user = User::requestJoinByEmail(
            $id,
            $date,
            $email,
            $hash,
            $token
        );

        $this->assertEquals($id, $user->getId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($date, $user->getDate());
        $this->assertEquals($hash, $user->getPasswordHash());
        $this->assertEquals($token, $user->getJoinConfirmToken());

        $this->assertTrue($user->isWait());
        $this->assertFalse($user->isActive());
    }
}
