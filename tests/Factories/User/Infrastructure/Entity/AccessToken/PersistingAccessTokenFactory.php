<?php

namespace App\Tests\Factories\User\Infrastructure\Entity\AccessToken;

use App\User\Application\Dto\TokenPayload;
use App\User\Application\Service\AuthTokenServiceInterface;
use App\User\Infrastructure\Entity\AccessToken\AccessToken;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<AccessToken>
 */
final class PersistingAccessTokenFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct(
        private AuthTokenServiceInterface $authTokenService
    )
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return AccessToken::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'revoked' => self::faker()->boolean(),
            'siteId' => Uuid::v4(),
            'token' => self::faker()->text(300),
            'userId' => Uuid::v4(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // it persists inside authTokenService->encode
            ->withoutPersisting()
            ->afterInstantiate(function(AccessToken $accessToken): void {
                $tokenPayload = new TokenPayload($accessToken->getUserId());
                $accessToken->setToken(
                    $this->authTokenService->encode($tokenPayload)
                );
            })
        ;
    }
}
