<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\Shared\Domain\Entity\Entity;
use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\GeoLocation;
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
    /** @var UserRole[] */
    #[ORM\Column(type: 'json', enumType: UserRole::class)]
    private array $roles = [];
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $firstName;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $lastName;
    #[ORM\Embedded(class: Address::class, columnPrefix: false)]
    private ?Address $address;
    #[ORM\Embedded(class: GeoLocation::class, columnPrefix: false)]
    private ?GeoLocation $geoLocation;

    /**
     * @param UserRole[] $roles
     */
    public function __construct(
        Uuid $userId,
        string $email,
        string $password,
        array $roles = [UserRole::REGULAR_USER],
        ?string $firstName = null,
        ?string $lastName = null,
        ?Address $address = null,
        ?GeoLocation $geoLocation = null
    ) {
        parent::__construct($userId);

        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address = $address;
        $this->geoLocation = $geoLocation;
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

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): void
    {
        $this->address = $address;
    }

    public function getGeoLocation(): ?GeoLocation
    {
        return $this->geoLocation;
    }

    public function setGeoLocation(?GeoLocation $geoLocation): void
    {
        $this->geoLocation = $geoLocation;
    }

    public function update(
        string $email,
        ?string $firstName = null,
        ?string $lastName = null,
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
