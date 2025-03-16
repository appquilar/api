<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\Shared\Domain\Entity\Entity;
use App\Shared\Infrastructure\Security\UserRole;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[Index(name: "email_idx", columns: ["email"])]
class User extends Entity
{
    #[ORM\Column(type: 'string', unique: true)]
    private string $email;
    #[ORM\Column(type: 'string')]
    private string $password;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $wordpressPassword = null;
    /** @var UserRole[] */
    #[ORM\Column(type: 'json', enumType: UserRole::class)]
    private array $roles = [];
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $firstName;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $lastName;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $wordpressId;

    /**
     * @param UserRole[] $roles
     */
    public function __construct(
        Uuid $userId,
        string $email,
        string $password,
        array $roles = [UserRole::REGULAR_USER],
        string $firstName = null,
        string $lastName = null,
    ) {
        parent::__construct($userId);

        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return UserRole[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param UserRole[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getWordpressPassword(): ?string
    {
        return $this->wordpressPassword;
    }

    public function setWordpressPassword(?string $wordpressPassword): void
    {
        $this->wordpressPassword = $wordpressPassword;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function update(
        string $email,
        string $firstName = null,
        string $lastName = null,
    ): void
    {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function hasDifferentRoles(array $newRoles): bool
    {
        $currentRoles = array_map(fn (UserRole $role) => $role->value, $this->roles);
        $incomingRoles = array_map(fn (UserRole $role) => $role->value, $newRoles);

        sort($currentRoles);
        sort($incomingRoles);

        return $currentRoles !== $incomingRoles;
    }
}
