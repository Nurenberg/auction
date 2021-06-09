<?php

declare(strict_types=1);

namespace App\Auth\Command\ResetPassword\Request;

class Command
{
    public string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }
}
