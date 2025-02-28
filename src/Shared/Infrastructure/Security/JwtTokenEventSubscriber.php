<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Infrastructure\Service\JsonResponseService;
use App\User\Application\Dto\TokenPayload;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\AuthTokenServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class JwtTokenEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AuthTokenServiceInterface $authTokenService,
        private UserRepositoryInterface   $userRepository,
        private UserGranted               $userGranted,
        private JsonResponseService       $jsonResponseService
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        $route = RoutePermission::tryFrom($path);
        if (!$route) {
            return;
        }

        $token = $request->headers->get('Authorization');
        if (!$token) {
            $event->setResponse($this->jsonResponseService->unauthorized('Nonexistent token'));
            return;
        }

        $token = str_replace('Bearer ', '', $token);

        try {
            $decodedToken = $this->authTokenService->decode($token);
            $this->validateToken($decodedToken);
        } catch (UnauthorizedException $e) {
            $event->setResponse($this->jsonResponseService->unauthorized($e->getMessage()));
            return;
        }

        $this->userGranted->setUser(
            $this->userRepository->findById($decodedToken->getUserId())
        );
        $this->userGranted->setToken($token);

        try {
            $this->validatePermissions($route);
        } catch (UnauthorizedException $e) {
            $event->setResponse($this->jsonResponseService->unauthorized($e->getMessage()));
            return;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    private function validatePermissions(RoutePermission $route): void
    {
        $canAccess = false;
        foreach ($this->userGranted->getUser()->getRoles() as $userRole) {
            $canAccess = $canAccess || $userRole->canAccess($route->getRequiredRoles());
        }
        if (!$canAccess) {
            throw new UnauthorizedException('Access denied for this role');
        }
    }

    public function validateToken(TokenPayload $decodedToken): void
    {
        if (
            $decodedToken->isRevoked() ||
            $decodedToken->isExpired()
        ) {
            throw new UnauthorizedException('Token is revoked or expired');
        }
    }
}
