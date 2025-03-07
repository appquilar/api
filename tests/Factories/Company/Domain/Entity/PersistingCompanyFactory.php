<?php

namespace App\Tests\Factories\Company\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\Shared\Domain\ValueObject\PhoneNumber;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Company>
 */
class PersistingCompanyFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Company::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'companyId' => Uuid::v4(),
            'name' => self::faker()->company(),
            'description' => self::faker()->text(),
            'ownerId' => Uuid::v4(),
            'fiscalIdentifier' => self::faker()->ean13(),
            'address' => self::faker()->streetAddress(),
            'postalCode' => self::faker()->postcode(),
            'city' => self::faker()->city(),
            'contactEmail' => self::faker()->companyEmail(),
            'phoneNumber' => new PhoneNumber(
                self::faker()->countryCode(),
                self::faker()->countryISOAlpha3(),
                self::faker()->phoneNumber()
            )
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this;
    }
}
