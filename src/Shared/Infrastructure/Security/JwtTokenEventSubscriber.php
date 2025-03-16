<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Infrastructure\Service\ResponseService;
use App\User\Application\Dto\TokenPayload;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Service\AuthTokenServiceInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class JwtTokenEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AuthTokenServiceInterface      $authTokenService,
        private UserRepositoryInterface        $userRepository,
        private CompanyRepositoryInterface     $companyRepository,
        private CompanyUserRepositoryInterface $companyUserRepository,
        private UserGranted                    $userGranted,
        private ResponseService                $jsonResponseService,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        $route = $this->getRoute($path, $request);

        try {
            $this->processAccessToken($request, $event);
        } catch (UnauthorizedException $e) {
            if ($route && $route->getRequiredRoles() !== []) {
                $event->setResponse($this->jsonResponseService->unauthorized($e->getMessage()));
                return;
            }
        }

        if (!$route || $route->getRequiredRoles() === []) {
            return;
        }

        try {
            $this->validatePermissions($route);
        } catch (UnauthorizedException $e) {
            $event->setResponse($this->jsonResponseService->unauthorized($e->getMessage()));
            return;
        }
    }

    private function processAccessToken(Request $request, RequestEvent $event): void
    {
        $token = $request->headers->get('Authorization');
        if (!$token) {
            throw new UnauthorizedException('Nonexistent token');
        }

        $token = str_replace('Bearer ', '', $token);
        $decodedToken = $this->authTokenService->decode($token);
        $this->validateToken($decodedToken);

        $this->userGranted->setUser($this->userRepository->findById($decodedToken->getUserId()));
        $this->userGranted->setCompanyUser(
            $this->companyUserRepository->findOneBy(['userId' => $decodedToken->getUserId()])
        );
        if ($this->userGranted->getCompanyUser() !== null) {
            $this->userGranted->setCompany(
                $this->companyRepository->findById($this->userGranted->getCompanyUser()->getCompanyId())
            );
        }
        $this->userGranted->setToken($token);
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

    /**
     * @param string $path
     * @param Request $request
     * @return RoutePermission|null
     */
    public function getRoute(string $path, Request $request): ?RoutePermission
    {
        $route = RoutePermission::tryFrom($path);
        if (!$route) {
            $routeName = $request->attributes->get('_route');
            $route = RoutePermission::tryFrom($routeName);
        }
        return $route;
    }
}
