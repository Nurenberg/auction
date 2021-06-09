<?php

declare(strict_types=1);

namespace Auth\Command\ChangeRole;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\UserRepository;
use App\Flusher;
use App\Auth\Entity\User\Role;

class Handler
{
    private UserRepository $users;
    private Flusher $flusher;

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $user->changeRole(
            new Role($command->role)
        );

        $this->flusher->flush();
    }
}
