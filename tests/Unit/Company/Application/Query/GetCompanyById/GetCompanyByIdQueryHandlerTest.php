<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Application\Query\GetCompanyById;

use App\Company\Application\Query\GetCompanyById\GetCompanyByIdQuery;
use App\Company\Application\Query\GetCompanyById\GetCompanyByIdQueryHandler;
use App\Company\Application\Query\GetCompanyById\GetCompanyByIdQueryResult;
use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Company\Application\Transformer\CompanyTransformer;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Tests\Factories\Company\Domain\Entity\CompanyFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class GetCompanyByIdQueryHandlerTest extends UnitTestCase
{
    private CompanyRepositoryInterface|MockObject $companyRepository;
    private CompanyTransformer|MockObject $companyTransformer;
    private GetCompanyByIdQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->companyTransformer = $this->createMock(CompanyTransformer::class);
        $this->handler = new GetCompanyByIdQueryHandler(
            $this->companyRepository,
            $this->companyTransformer,
        );
    }

    public function testNonExistentCompanyWillThrownANotFoundError(): void
    {
        $nonexistentCompanyId = Uuid::v4();

        $this->companyRepository->expects($this->once())
            ->method('findById')
            ->with($nonexistentCompanyId)
            ->willReturn(null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage("Entity with id " . $nonexistentCompanyId->toString() . " not found");

        $this->handler->__invoke(new GetCompanyByIdQuery($nonexistentCompanyId));
    }

    public function testReturnCompanyAfterTransformation(): void
    {
        $companyId = Uuid::v4();
        $company = CompanyFactory::createOne(['companyId' => $companyId]);
        $expectedResult = ['company_id' => $company->getId()->toString()];

        $this->companyRepository->expects($this->once())
            ->method('findById')
            ->with($companyId)
            ->willReturn($company);

        $this->companyTransformer->expects($this->once())
            ->method('transform')
            ->with($company)
            ->willReturn($expectedResult);

        /** @var GetCompanyByIdQueryResult $result */
        $result = $this->handler->__invoke(new GetCompanyByIdQuery($companyId));

        $this->assertEquals($expectedResult, $result->getCompany());
    }
}
