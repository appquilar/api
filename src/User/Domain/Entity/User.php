<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\Shared\Domain\Entity\Entity;
use App\Shared\Infrastructure\Security\UserRole;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[Index(name: "email_idx", columns: ["email"])]
class User extends Entity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $wordpressPassword = null;

    #[ORM\Column(type: 'json', enumType: UserRole::class)]
    private array $roles = [];

    public function __construct(
        Uuid $userId,
        string $email,
        string $password,
        array $roles = [UserRole::REGULAR_USER]
    ) {
        parent::__construct($userId);

        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
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

    public function eraseCredentials(): void
    {
        // No temporary sensitive data stored
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
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
}
