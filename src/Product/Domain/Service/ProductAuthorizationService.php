<?php

declare(strict_types=1);

namespace App\Product\Domain\Service;

use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Domain\Entity\Product;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Infrastructure\Security\UserRole;
use App\User\Domain\Entity\User;
use Symfony\Component\Uid\Uuid;

class ProductAuthorizationService implements ProductAuthorizationServiceInterface
{
    public function __construct(
        private CompanyUserServiceInterface $companyUserService,
        private UserGranted $userGranted,
    ) {
    }

    public function canView(Product $product, string $errorMessage): void
    {
        if (!$this->hasPermissions($product)) {
            throw new UnauthorizedException($errorMessage);
        }
    }

    public function canViewIfPublic(Product $product, string $errorMessage): void
    {
        if (!$product->isPublished()) {
            throw new UnauthorizedException($errorMessage);
        }
    }

    public function canEdit(Product $product, string $errorMessage): void
    {
        if (!$this->hasPermissions($product)) {
            throw new UnauthorizedException($errorMessage);
        }
    }

    /**
     * @throws UnauthorizedException
     */
    public function assignOwnership(Product $product, ?Uuid $companyId = null): void
    {
        $user = $this->getUserGranted();

        if ($companyId !== null && $this->companyUserService->userBelongsToCompany($user->getId(), $companyId)) {
            $product->setCompanyId($companyId);
            return;
        }

        if ($companyId !== null) {
            throw new UnauthorizedException();
        }

        $product->setUserId($user->getId());
    }

    /**
     * @throws UnauthorizedException
     */
    private function getUserGranted(): User
    {
        $user = $this->userGranted->getUser();

        if ($user === null) {
            throw new UnauthorizedException('You must be logged in to create a product');
        }

        return $user;
    }

    private function hasPermissions(Product $product): bool
    {
        if ($this->userGranted->getUser() === null) {
            return false;
        }

        $userId = $this->userGranted->getUser()->getId();

        if ($product->belongsToUser() && $product->getUserId()->equals($userId)) {
            return true;
        }

        return
            $product->belongsToCompany() &&
            $this->companyUserService->userBelongsToCompany($userId, $product->getCompanyId());
    }
}
