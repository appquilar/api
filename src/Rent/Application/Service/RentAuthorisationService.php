<?php declare(strict_types=1);

namespace App\Rent\Application\Service;

use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Enum\RentOwnerType;
use App\Rent\Domain\Enum\RentStatus;
use App\Rent\Domain\Service\RentAuthorisationServiceInterface;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use Symfony\Component\Uid\Uuid;

class RentAuthorisationService implements RentAuthorisationServiceInterface
{

    public function __construct(
        private UserGranted $userGranted,
        private RentCompanyUserServiceInterface $rentCompanyUserService,
    ) {
    }

    public function canCreate(Rent $rent): void
    {
        $this->onlyIfOwnerAndRenter(
            $this->userGranted->getUser()->getId(),
            $rent,
            'rent.user.cannot_create'
        );
    }

    public function canView(Rent $rent): void
    {
        $this->onlyIfOwnerAndRenter(
            $this->userGranted->getUser()->getId(),
            $rent,
            'rent.user.cannot_view'
        );
    }

    public function canEdit(Rent $rent): void
    {
        $this->onlyIfOwner(
            $this->userGranted->getUser()->getId(),
            $rent,
            'rent.user.cannot_edit'
        );
    }

    public function canChangeStatus(Rent $rent, RentStatus $newState): void
    {
        if ($newState === RentStatus::CANCELLED) {
            $this->onlyIfOwnerAndRenter(
                $this->userGranted->getUser()->getId(),
                $rent,
                'rent.user.cannot_cancel'
            );
            return;
        }

        $this->onlyIfOwner(
            $this->userGranted->getUser()->getId(),
            $rent,
            'rent.user.cannot_change_state'
        );
    }

    public function canChangePrice(Rent $rent): void
    {
        $this->onlyIfOwner(
            $this->userGranted->getUser()->getId(),
            $rent,
            'rent.user.cannot_change_price'
        );
    }

    private function onlyIfOwnerAndRenter(Uuid $userId, Rent $rent, string $action): void
    {
        if (
            !$this->checkIfUserGrantedIsRenter($userId, $rent) &&
            !$this->checkIfUserGrantedIsOwner($userId, $rent)
        ) {
            throw new UnauthorizedException($action);
        }
    }

    private function onlyIfOwner(Uuid $userId, Rent $rent, string $action): void
    {
        if (
            !$this->checkIfUserGrantedIsOwner($userId, $rent)
        ) {
            throw new UnauthorizedException($action);
        }
    }

    private function checkIfUserGrantedIsRenter(Uuid $userId, Rent $rent): bool
    {
        return $rent->getRenterId()->equals($userId);
    }

    private function checkIfUserGrantedIsOwner(Uuid $userId, Rent $rent): bool
    {
        if ($rent->getOwnerType() === RentOwnerType::COMPANY) {
            return $this->rentCompanyUserService->userBelongsToCompany(
                $userId,
                $rent->getOwnerId()
            );
        } else {
            return $rent->getOwnerId()->equals($userId);
        }
    }
}
