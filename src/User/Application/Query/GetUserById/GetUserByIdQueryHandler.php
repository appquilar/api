<?php

declare(strict_types=1);

namespace App\User\Application\Query\GetUserById;

use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use App\User\Application\Repository\UserRepositoryInterface;
use App\User\Application\Transformer\UserTransformer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GetUserByIdQuery::class)]
class GetUserByIdQueryHandler implements QueryHandler
{

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserGranted $userGranted,
        private UserTransformer $transformer,
    ) {
    }

    public function __invoke(GetUserByIdQuery|Query $query): QueryResult
    {
        if ($this->userGranted->getUser()->getId() === $query->getUserId()) {
            return new GetUserByIdQueryResult(
                $this->transformer->transform($this->userGranted->getUser())
            );
        }

        return new GetUserByIdQueryResult(
            $this->transformer->transform(
                $this->userRepository->findById($query->getUserId())
            )
        );
    }
}
