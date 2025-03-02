<?php

namespace App\Tests\Factories\User\Infrastructure\Entity\ForgotPasswordToken;

use App\User\Infrastructure\Entity\ForgotPasswordToken\ForgotPasswordToken;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<ForgotPasswordToken>
 */
final class ForgotPasswordTokenFactory extends PersistingForgotPasswordTokenFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->withoutPersisting();
    }
}
