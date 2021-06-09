<?php

declare(strict_types=1);

namespace Auth\Command\ChangeEmail\Confirm;

use App\Auth\Entity\User\UserRepository;
use App\Flusher;
use DateTimeImmutable;
use DomainException;

class Handler
{
    private UserRepository $users;
    private Flusher $flusher;

    public function __construct(
        UserRepository $users,
        Flusher $flusher
    ) {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $user = $this->users->findByNewEmailToken($command->token);

        if (!$user) {
            throw new DomainException('Incorrect token.');
        }

        $user->confirmEmailChanging(
            $command->token,
            new DateTimeImmutable()
        );

        $this->flusher->flush();
    }
}