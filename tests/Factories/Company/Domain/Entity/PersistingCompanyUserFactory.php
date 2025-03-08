<?php

namespace App\Tests\Factories\Company\Domain\Entity;

use App\Company\Domain\Entity\CompanyUser;
use App\Company\Domain\Enum\CompanyUserRole;
use App\Company\Domain\Enum\CompanyUserStatus;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<CompanyUser>
 */
class PersistingCompanyUserFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return CompanyUser::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'companyId' => Uuid::v4(),
            'userId' => Uuid::v4(),
            'role' => CompanyUserRole::CONTRIBUTOR,
            'email' => self::faker()->email(),
            'status' => CompanyUserStatus::PENDING,
            'invitationExpiresAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('+1 day', '+1 month')),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(CompanyUser $companyUser): void {})
        ;
    }
}
