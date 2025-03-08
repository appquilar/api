<?php

declare(strict_types=1);

namespace App\Tests\Factories\User\Domain\Entity;

use App\User\Domain\Entity\User;
use Hautelook\Phpass\PasswordHash;

/**
 * @method static User createOne(array $attributes = [])
 */
final class UserFactory extends PersistingUserFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->withoutPersisting()
            ->afterInstantiate(function(User $user) {
                $wpHasher = new PasswordHash(8, true);
                $user->setPassword($this->passwordHasher->hashPassword($user->getPassword()));
                if ($user->getWordpressPassword() !== null) {
                    $user->setWordpressPassword(
                        $wpHasher->HashPassword($user->getWordpressPassword())
                    );
                }
            });
    }
}
