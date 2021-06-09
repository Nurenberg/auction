<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class NetworkIdentity
{
    private string $network;
    private string $identity;

    public function __construct(string $network, string $identity)
    {
        Assert::notEmpty($network);
        Assert::notEmpty($identity);
        $this->network = (string)mb_strtolower($network);
        $this->identity = (string)mb_strtolower($identity);
    }

    public function isEqualTo(self $network): bool
    {
        return
            $this->getIdentity() == $network->getIdentity() &&
            $this->getNetwork() == $network->getNetwork();
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
}
