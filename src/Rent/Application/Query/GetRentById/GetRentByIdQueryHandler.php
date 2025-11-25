<?php declare(strict_types=1);

namespace App\Rent\Application\Query\GetRentById;

use App\Rent\Application\Repository\RentRepositoryInterface;
use App\Rent\Application\Transformer\RentTransformer;
use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Service\RentAuthorisationServiceInterface;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GetRentByIdQuery::class)]
class GetRentByIdQueryHandler implements QueryHandler
{
    public function __construct(
        private RentRepositoryInterface $rentRepository,
        private RentAuthorisationServiceInterface $authorizationService,
        private RentTransformer $transformer
    ) {
    }

    public function __invoke(Query|GetRentByIdQuery $query): GetRentByIdQueryResult
    {
        /** @var Rent|null $rent */
        $rent = $this->rentRepository->findById($query->getRentId());

        if ($rent === null) {
            throw new EntityNotFoundException($query->getRentId());
        }

        $this->authorizationService->canView($rent);

        return new GetRentByIdQueryResult(
            $this->transformer->transform($rent)
        );
    }
}
