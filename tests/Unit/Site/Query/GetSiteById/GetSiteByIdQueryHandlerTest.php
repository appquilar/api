<?php

declare(strict_types=1);

namespace App\Tests\Unit\Site\Query\GetSiteById;

use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Site\Application\Query\GetSiteById\GetSiteByIdQuery;
use App\Site\Application\Query\GetSiteById\GetSiteByIdQueryHandler;
use App\Site\Application\Repository\SiteRepositoryInterface;
use App\Site\Application\Transformer\SiteTransformer;
use App\Tests\Factories\Site\Domain\Entity\SiteFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class GetSiteByIdQueryHandlerTest extends UnitTestCase
{
    private SiteRepositoryInterface|MockObject $siteRepositoryMock;
    private SiteTransformer|MockObject $transformerMock;
    private GetSiteByIdQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->siteRepositoryMock = $this->createMock(SiteRepositoryInterface::class);
        $this->transformerMock = $this->createMock(SiteTransformer::class);
        $this->handler = new GetSiteByIdQueryHandler($this->siteRepositoryMock, $this->transformerMock);
    }

    public function testGetSiteByIdSuccessful(): void
    {
        $siteId = Uuid::v4();
        $site = SiteFactory::createOne(['siteId' => $siteId]);
        $transformerResponse = ['site_id' => $siteId->toString()];

        $this->siteRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($siteId)
            ->willReturn($site);

        $this->transformerMock->expects($this->once())
            ->method('transform')
            ->with($site)
            ->willReturn($transformerResponse);

        $query = new GetSiteByIdQuery($siteId);
        $this->handler->__invoke($query);
    }

    public function testSiteNotFound(): void
    {
        $siteId = Uuid::v4();

        $this->siteRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($siteId)
            ->willReturn(null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Entity with id ' . $siteId->toString() . ' not found');

        $query = new GetSiteByIdQuery($siteId);
        $this->handler->__invoke($query);
    }
}
