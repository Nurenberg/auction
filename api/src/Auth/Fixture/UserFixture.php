<?php

declare(strict_types=1);

namespace App\Auth\Fixture;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class UserFixture extends AbstractFixture
{
    private const PASSWORD_HASH = '$2y12$qwnND33o8DGWvFoepotSju723asdnnSAD2asd$heEkk82hhlFLN23';

    public function load(ObjectManager $manager): void
    {
        $user = User::requestJoinByEmail(
            new Id('00000000-0000-0000-0000-000000000001'),
            $date = new DateTimeImmutable(),
            new Email('user@app.test'),
            self::PASSWORD_HASH,
            new Token($value = Uuid::uuid4()->__toString(), $date->modify('+1 day'))
        );

        $user->confirmJoin($value, $date);

        $manager->persist($user);
        $manager->flush();
    }
}
