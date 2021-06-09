<?php

declare(strict_types=1);

namespace Auth\Test\Unit\Entity\User\User;

use App\Auth\Test\Builder\UserBuilder;
use App\Auth\Entity\User\Role;
use PHPUnit\Framework\TestCase;

class ChangeRole extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->build();

        $role = new Role(Role::ADMIN);
        $user->changeRole($role);

        self::assertEquals($role, $user->getRole());
    }
}