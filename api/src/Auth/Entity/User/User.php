<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Service\PasswordHasher;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="auth_users")
 */
class User
{
    /**
     * @ORM\Column(type="auth_user_id")
     * @ORM\Id
     */
    private Id $id;
    /**
     * @ORM\Column(type="auth_user_email", unique=true)
     */
    private Email $email;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $passwordHash = null;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $date;
    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $joinConfirmToken = null;
    /**
     * @ORM\Column(type="auth_user_status", length=16)
     */
    private Status $status;
    /**
     * @ORM\OneToMany(targetEntity="UserNetwork", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */
    private Collection $networks;
    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $passwordResetToken = null;
    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $newEmailToken = null;
    /**
     * @ORM\Column(type="auth_user_email", nullable=true)
     */
    private ?Email $newEmail = null;
    /**
     * @ORM\Column(type="auth_user_role", length=16)
     */
    private Role $role;

    public function __construct(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        Status $status
    )
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->status = $status;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    public static function requestJoinByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $hash,
        Token $joinConfirmToken
    ): self
    {
        $user = new User($id, $date, $email, Status::wait());
        $user->passwordHash = $hash;
        $user->joinConfirmToken = $joinConfirmToken;

        return $user;
    }

    public static function joinByNetwork(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        Network $network
    ): self
    {
        $user = new self($id, $date, $email, Status::active());
        $user->networks->add(new UserNetwork($user, $network));
        return $user;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getJoinConfirmToken(): ?Token
    {
        return $this->joinConfirmToken;
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function confirmJoin(string $token, DateTimeImmutable $date): void
    {
        if (is_null($this->joinConfirmToken)) {
            throw new DomainException('Confirmation is not required.');
        }

        $this->joinConfirmToken->validate($token, $date);
        $this->status = Status::active();
        $this->joinConfirmToken = null;
    }

    /**
     * @return Network[]
     */
    public function getNetworks(): array
    {
        /** @var Network[] */
        return $this->networks->map(static function(UserNetwork $userNetwork) {
            return $userNetwork->getNetwork();
        })->toArray();
    }

    public function getPasswordResetToken(): ?Token
    {
        return $this->passwordResetToken;
    }

    public function getNewEmailToken(): ?Token
    {
        return $this->newEmailToken;
    }

    public function setNewEmailToken(?Token $newEmailToken): self
    {
        $this->newEmailToken = $newEmailToken;

        return $this;
    }

    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }

    public function setNewEmail(?Email $newEmail): self
    {
        $this->newEmail = $newEmail;

        return $this;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function attachNetwork(Network $identity): void
    {
        /** @var UserNetwork $network */
        foreach ($this->networks as $network) {
            if ($network->getNetwork()->isEqualTo($identity)) {
                throw new DomainException('Network is already attached.');
            }
        }

        $this->networks->add(new UserNetwork($this, $identity));
    }

    public function requestResetPassword(Token $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active.');
        }

        if (!is_null($this->passwordResetToken) && !$this->passwordResetToken->isExpiredTo($date)) {
            throw new DomainException('Resetting is already requested.');
        }

        $this->passwordResetToken = $token;
    }

    public function resetPassword(string $token, DateTimeImmutable $date, string $hash): void
    {
        if (is_null($this->passwordResetToken)) {
            throw new DomainException('Resetting is not requested.');
        }
        $this->passwordResetToken->validate($token, $date);
        $this->passwordResetToken = null;
        $this->passwordHash = $hash;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher): void
    {
        if (!$this->getPasswordHash()) {
            throw new DomainException('User does not have an old password.');
        }

        if (!$hasher->validate($current, $new)) {
            throw new DomainException('Incorrect current password');
        }

        $this->passwordHash = $hasher->hash($new);
    }

    public function requestEmailChanging(Token $token, DateTimeImmutable $date, Email $email): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active.');
        }
        if ($this->email->isEqualTo($email)) {
            throw new DomainException('Email is already same.');
        }
        if ($this->newEmailToken !== null && !$this->newEmailToken->isExpiredTo($date)) {
            throw new DomainException('Changing is already requested.');
        }
        $this->newEmail = $email;
        $this->newEmailToken = $token;
    }

    public function confirmEmailChanging(string $token, DateTimeImmutable $date): void
    {
        if (is_null($this->newEmailToken) || is_null($this->newEmail)) {
            throw new DomainException('Changing is not requested.');
        }

        $this->newEmailToken->validate($token, $date);
        $this->email = $this->newEmail;
        $this->newEmailToken = null;
        $this->newEmail = null;
    }

    public function changeRole(Role $role): void
    {
        $this->role = $role;
    }

    public function remove(): void
    {
        if (!$this->isWait()) {
            throw new DomainException('Unable to remove active user.');
        }
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if ($this->joinConfirmToken && $this->joinConfirmToken->isEmpty()) {
            $this->joinConfirmToken = null;
        }
        if ($this->passwordResetToken && $this->passwordResetToken->isEmpty()) {
            $this->passwordResetToken = null;
        }
        if ($this->newEmailToken && $this->newEmailToken->isEmpty()) {
            $this->joinConfirmToken = null;
        }
    }
}
