<?php

declare(strict_types=1);

namespace App\Rent\Application\Command\UpdateRent;

use App\Rent\Application\Repository\RentRepositoryInterface;
use App\Rent\Domain\Service\RentAuthorisationServiceInterface;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: UpdateRentCommand::class)]
class UpdateRentCommandHandler implements CommandHandler
{
    public function __construct(
        private RentRepositoryInterface $rentRepository,
        private RentAuthorisationServiceInterface $rentAuthorisationService,
    ) {
    }

    public function __invoke(Command|UpdateRentCommand $command): void
    {
        $rent = $this->rentRepository->findById($command->getRentId());

        if ($rent === null) {
            throw new EntityNotFoundException($command->getRentId());
        }

        $this->rentAuthorisationService->canEdit($rent);

        $rent->setStartDate($command->getStartDate());
        $rent->setEndDate($command->getEndDate());

        if ($command->getDeposit() !== null) {
            $this->rentAuthorisationService->canChangePrice($rent);
            $rent->setDeposit($command->getDeposit());
        }

        if ($command->getPrice() !== null) {
            $this->rentAuthorisationService->canChangePrice($rent);
            $rent->setPrice($command->getPrice());
        }

        if ($command->getDepositReturned() !== null) {
            $this->rentAuthorisationService->canChangePrice($rent);
            $rent->setDepositReturned($command->getDepositReturned());
        }

        $this->rentRepository->save($rent);
    }
}
