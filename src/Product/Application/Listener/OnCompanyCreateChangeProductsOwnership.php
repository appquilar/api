<?php declare(strict_types=1);

namespace App\Product\Application\Listener;

use App\Company\Application\Event\CompanyCreated;
use App\Product\Application\Command\MigrateOwnershipFromUserToCompany\MigrateOwnershipFromUserToCompanyCommand;
use App\Shared\Application\Command\CommandBus;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

#[AsEventListener(event: CompanyCreated::class)]
class OnCompanyCreateChangeProductsOwnership
{
    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(CompanyCreated $event): void
    {
        $this->commandBus->dispatch(
            new MigrateOwnershipFromUserToCompanyCommand(
                $event->getOwnerId(),
                $event->getCompanyId()
            )
        );
    }
}
