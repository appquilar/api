<?php

declare(strict_types=1);

namespace App\Product\Domain\Service;

use App\Product\Application\Service\ProductAuthorizationServiceInterface;
use App\Product\Domain\Entity\Product;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Infrastructure\Security\UserRole;

class ProductAuthorizationService implements ProductAuthorizationServiceInterface
{
    public function __construct(
        private CompanyUserServiceInterface $companyUserService,
        private UserGranted $userGranted,
    ) {
    }

    /**
     * Check if a user has permission to view a product
     */
    public function canView(Product $product): bool
    {
        // Published products can be viewed by anyone
        if ($product->isPublished()) {
            return true;
        }

        return $this->hasPermissions($product);
    }

    /**
     * Check if a user has permission to edit a product
     */
    public function canEdit(Product $product): bool
    {
        return $this->hasPermissions($product);
    }

    private function hasPermissions(Product $product): bool
    {
        // If the user is not authenticated, return false
        if ($this->userGranted->getUser() === null) {
            return false;
        }

        $userId = $this->userGranted->getUser()->getId();
        $userRoles = $this->userGranted->getUser()->getRoles();

        // Admin users have permissions over all products
        if (in_array(UserRole::ADMIN->value, $userRoles)) {
            return true;
        }

        // User have permissions their own products
        if ($product->belongsToUser() && $product->getUserId()->equals($userId)) {
            return true;
        }

        // Check if user belongs to the company that owns the product
        return
            $product->belongsToCompany() &&
            $this->companyUserService->userBelongsToCompany($userId, $product->getCompanyId());
    }
}
