<?php

declare(strict_types=1);

namespace App\Rent\Application\Command\CreateRent;

use App\Rent\Application\Repository\RentRepositoryInterface;
use App\Rent\Application\Service\RentProductServiceInterface;
use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Enum\RentStatus;
use App\Rent\Domain\Service\RentAuthorisationServiceInterface;
use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Domain\ValueObject\Money;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: CreateRentCommand::class)]
class CreateRentCommandHandler implements CommandHandler
{
    public function __construct(
        private RentRepositoryInterface $rentRepository,
        private RentAuthorisationServiceInterface $rentAuthorisationService,
        private RentProductServiceInterface $rentProductService,
    ) {
    }

    public function __invoke(Command|CreateRentCommand $command): void
    {
        $product = $this->rentProductService->getProductOwnershipByProductId($command->getProductId());

        if ($product === null) {
            throw new BadRequestException('rent.create.product.not_found');
        }

        $rent = new Rent(
            $command->getRentId(),
            $command->getProductId(),
            $product->getOwnerId(),
            $product->getOwnerType(),
            $command->getRenterId(),
            $command->getStartDate(),
            $command->getEndDate(),
            $command->getDeposit(),
            $command->getPrice(),
            Money::zero(),
            RentStatus::draft()
        );

        $this->rentAuthorisationService->canCreate($rent);

        $this->rentRepository->save($rent);
    }
}
