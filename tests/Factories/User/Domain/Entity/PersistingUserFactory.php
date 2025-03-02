<?php

namespace App\Tests\Factories\User\Domain\Entity;

use App\Shared\Infrastructure\Security\UserRole;
use App\User\Application\Service\UserPasswordHasher;
use App\User\Domain\Entity\User;
use Hautelook\Phpass\PasswordHash;
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
            'last_name' => self::faker()->lastName()
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(User $user) {
                $wpHasher = new PasswordHash(8, true);
                $user->setPassword($this->passwordHasher->hashPassword($user->getPassword()));
                if ($user->getWordpressPassword() !== null) {
                    $user->setWordpressPassword($wpHasher->HashPassword($user->getWordpressPassword()));
                }
            });
    }
}
