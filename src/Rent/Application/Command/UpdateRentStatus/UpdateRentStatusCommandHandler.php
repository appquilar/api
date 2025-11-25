<?php declare(strict_types=1);

namespace App\Rent\Application\Command\UpdateRentStatus;

use App\Rent\Application\Repository\RentRepositoryInterface;
use App\Rent\Domain\Service\RentAuthorisationServiceInterface;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: UpdateRentStatusCommand::class)]
class UpdateRentStatusCommandHandler implements CommandHandler
{
    public function __construct(
        private RentRepositoryInterface $rentRepository,
        private RentAuthorisationServiceInterface $rentAuthorisationService,
    ) {
    }

    public function __invoke(Command|UpdateRentStatusCommand $command): void
    {
        $rent = $this->rentRepository->findById($command->getRentId());

        if ($rent === null) {
            throw new EntityNotFoundException($command->getRentId());
        }

        $this->rentAuthorisationService->canChangeStatus($rent, $command->getRentStatus());

        $rent->transitionTo($command->getRentStatus());

        $this->rentRepository->save($rent);
    }
}
