<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Application\Query\GetCompanyBySlug;

use App\Company\Application\Query\GetCompanyBySlug\GetCompanyBySlugQuery;
use App\Company\Application\Query\GetCompanyBySlug\GetCompanyBySlugQueryHandler;
use App\Company\Application\Query\GetCompanyBySlug\GetCompanyBySlugQueryResult;
use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Company\Application\Transformer\CompanyTransformer;
use App\Shared\Application\Exception\NotFound\NotFoundException;
use App\Tests\Factories\Company\Domain\Entity\CompanyFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class GetCompanyBySlugQueryHandlerTest extends UnitTestCase
{
    private CompanyRepositoryInterface|MockObject $companyRepository;
    private CompanyTransformer|MockObject $companyTransformer;
    private GetCompanyBySlugQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyRepository = $this->createMock(CompanyRepositoryInterface::class);
        $this->companyTransformer = $this->createMock(CompanyTransformer::class);
        $this->handler = new GetCompanyBySlugQueryHandler(
            $this->companyRepository,
            $this->companyTransformer,
        );
    }

    public function testNonExistentCompanyWillThrownANotFoundError(): void
    {
        $nonexistentCompanySlug = 'acme-inc';

        $this->companyRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['slug' => $nonexistentCompanySlug])
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->handler->__invoke(new GetCompanyBySlugQuery($nonexistentCompanySlug));
    }

    public function testReturnCompanyAfterTransformation(): void
    {
        $companyId = Uuid::v4();
        $slug = 'acme-inc';
        $company = CompanyFactory::createOne(['companyId' => $companyId, 'slug' => $slug]);
        $expectedResult = ['company_id' => $company->getId()->toString(), 'slug' => $slug];

        $this->companyRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['slug' => $slug])
            ->willReturn($company);

        $this->companyTransformer->expects($this->once())
            ->method('transform')
            ->with($company)
            ->willReturn($expectedResult);

        /** @var GetCompanyBySlugQueryResult $result */
        $result = $this->handler->__invoke(new GetCompanyBySlugQuery($slug));

        $this->assertEquals($expectedResult, $result->getCompany());
    }
}
