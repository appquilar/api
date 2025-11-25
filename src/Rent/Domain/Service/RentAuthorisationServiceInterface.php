<?php declare(strict_types=1);

namespace App\Rent\Domain\Service;

use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Enum\RentStatus;

interface RentAuthorisationServiceInterface
{
    public function canCreate(Rent $rent): void;
    public function canView(Rent $rent): void;
    public function canEdit(Rent $rent): void;
    public function canChangeStatus(Rent $rent, RentStatus $newState): void;
    public function canChangePrice(Rent $rent): void;
}
