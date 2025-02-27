<?php

namespace App\Tests\Factories\User\Domain\Entity;

use App\User\Domain\Entity\User;
use Hautelook\Phpass\PasswordHash;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
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
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'userId' => Uuid::v4(),
            'email' => self::faker()->text(),
            'password' => self::faker()->text(),
            'roles' => ['REGULAR_USER'],
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
                $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
                if ($user->getWordpressPassword() !== null) {
                    $user->setWordpressPassword($wpHasher->HashPassword($user->getWordpressPassword()));
                }
            });
    }
}
