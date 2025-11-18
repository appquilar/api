<?php

namespace App\Tests\Factories\Company\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\GeoLocation;
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
            'slug' => self::faker()->slug(2),
            'description' => self::faker()->text(),
            'fiscalIdentifier' => self::faker()->ean13(),
            'contactEmail' => self::faker()->companyEmail(),
            'phoneNumber' => new PhoneNumber(
                self::faker()->countryCode(),
                self::faker()->countryISOAlpha3(),
                self::faker()->phoneNumber()
            ),
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
        return $this->with($this->defaults());
    }
}
