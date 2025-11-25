<?php declare(strict_types=1);

namespace App\Rent\Application\Query\ListRents;

use App\Rent\Application\Repository\RentRepositoryInterface;
use App\Rent\Application\Transformer\RentTransformer;
use App\Rent\Domain\Entity\Rent;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler(handles: ListRentsQuery::class)]
class ListRentsQueryHandler implements QueryHandler
{
    public function __construct(
        private RentRepositoryInterface $rentRepository,
        private RentTransformer $transformer,
        private UserGranted $userGranted
    ) {
    }

    public function __invoke(Query|ListRentsQuery $query): ListRentsQueryResult
    {
        $result = $this->rentRepository->searchByOwner(
            $this->getOwnerId($query->getOwnerId()),
            $query->getProductId(),
            $query->getStartDate(),
            $query->getEndDate(),
            $query->getStatus(),
            $query->getPage(),
            $query->getPerPage()
        );

        $rents = array_map(
            fn(Rent $rent) => $this->transformer->transform($rent),
            $result['items']
        );

        return new ListRentsQueryResult(
            $rents,
            $result['total'],
            $query->getPage(),
        );
    }

    private function getOwnerId(?Uuid $ownerId = null): Uuid
    {
        return $this->userGranted->isAdmin() && $ownerId !== null ?
            $ownerId :
            $this->userGranted->getUser()->getId();
    }
}
