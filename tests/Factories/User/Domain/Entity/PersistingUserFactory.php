<?php

namespace App\Tests\Factories\User\Domain\Entity;

use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\GeoLocation;
use App\Shared\Infrastructure\Security\UserRole;
use App\User\Application\Service\UserPasswordHasher;
use App\User\Domain\Entity\User;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class PersistingUserFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct(
        protected UserPasswordHasher $passwordHasher
    )
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'userId' => Uuid::v4(),
            'email' => self::faker()->email(),
            'password' => self::faker()->text(),
            'roles' => [UserRole::REGULAR_USER],
            'first_name' => self::faker()->name(),
            'last_name' => self::faker()->lastName(),
            'address' => new Address(
                self::faker()->streetAddress(),
                self::faker()->buildingNumber(),
                substr(self::faker()->city(), 0, 50),
                substr(self::faker()->postcode(), 0, 20),
                substr(self::faker()->country(), 0, 20),
                substr(self::faker()->country(), 0, 20)
            ),
            'geoLocation' => new GeoLocation(
                self::faker()->latitude(),
                self::faker()->longitude()
            )
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->with($this->defaults())
            ->afterInstantiate(function(User $user) {
                $user->setPassword($this->passwordHasher->hashPassword($user->getPassword()));
            });
    }
}
