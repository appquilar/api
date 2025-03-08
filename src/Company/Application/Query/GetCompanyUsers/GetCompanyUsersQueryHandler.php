<?php

declare(strict_types=1);

namespace App\Company\Application\Query\GetCompanyUsers;

use App\Company\Application\Repository\CompanyUserRepositoryInterface;
use App\Company\Application\Transformer\CompanyUserTransformer;
use App\Company\Domain\Entity\CompanyUser;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Application\Query\Query;
use App\Shared\Application\Query\QueryHandler;
use App\Shared\Application\Query\QueryResult;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: GetCompanyUsersQuery::class)]
class GetCompanyUsersQueryHandler implements QueryHandler
{

    public function __construct(
        private CompanyUserRepositoryInterface $companyUserRepository,
        private UserGranted $userGranted,
        private CompanyUserTransformer $transformer
    ) {
    }

    public function __invoke(GetCompanyUsersQuery|Query $query): QueryResult
    {
        if (
            $this->userGranted->getCompanyUser() === null &&
            !$this->userGranted->worksAtThisCompany($query->getCompanyId()) &&
            !$this->userGranted->isAdmin()
        ) {
            throw new UnauthorizedException();
        }

        $companyUsers = $this->companyUserRepository->findPaginatedUsersByCompanyId(
            $query->getCompanyId(),
            $query->getPage(),
            $query->getPerPage()
        );

        return new GetCompanyUsersQueryResult(
            array_map(
                function (CompanyUser $companyUser) {
                    return $this->transformer->transform($companyUser);
                },
                $companyUsers
            ),
            $this->companyUserRepository->countUsersByCompanyId($query->getCompanyId()),
            $query->getPage()
        );
    }
}
