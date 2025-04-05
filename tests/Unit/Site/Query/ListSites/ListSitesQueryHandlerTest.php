<?php

declare(strict_types=1);

namespace App\Tests\Unit\Site\Query\ListSites;

use App\Site\Application\Query\GetSiteById\GetSiteByIdQuery;
use App\Site\Application\Query\GetSiteById\GetSiteByIdQueryHandler;
use App\Site\Application\Query\ListSites\ListSitesQuery;
use App\Site\Application\Query\ListSites\ListSitesQueryHandler;
use App\Site\Application\Repository\SiteRepositoryInterface;
use App\Site\Application\Transformer\SiteTransformer;
use App\Tests\Factories\Site\Domain\Entity\SiteFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class ListSitesQueryHandlerTest extends UnitTestCase
{
    private SiteRepositoryInterface|MockObject $siteRepositoryMock;
    private SiteTransformer|MockObject $transformerMock;
    private ListSitesQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->siteRepositoryMock = $this->createMock(SiteRepositoryInterface::class);
        $this->transformerMock = $this->createMock(SiteTransformer::class);
        $this->handler = new ListSitesQueryHandler($this->siteRepositoryMock, $this->transformerMock);
    }

    public function testGetSiteByIdSuccessful(): void
    {
        $site1Id = Uuid::v4();
        $site2Id = Uuid::v4();
        $site1 = SiteFactory::createOne(['siteId' => $site1Id]);
        $site2 = SiteFactory::createOne(['siteId' => $site2Id]);
        $transformerResponse1 = ['site_id' => $site1Id->toString()];
        $transformerResponse2 = ['site_id' => $site2Id->toString()];

        $this->siteRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn([$site1, $site2]);

        $this->transformerMock->expects($this->exactly(2))
            ->method('transform')
            ->willReturnCallback(function ($site) use ($site1, $site2, $transformerResponse1, $transformerResponse2) {
                static $callCount = 0;
                if ($callCount === 0) {
                    $this->assertSame($site1, $site);
                    $callCount++;
                    return $transformerResponse1;
                }
                if ($callCount === 1) {
                    $this->assertSame($site2, $site);
                    $callCount++;
                    return $transformerResponse2;
                }
                throw new \LogicException('Unexpected call to transform()');
            });

        $query = new ListSitesQuery();
        $this->handler->__invoke($query);
    }

    public function testNoSites(): void
    {
        $this->siteRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->transformerMock->expects($this->never())
            ->method('transform');

        $query = new ListSitesQuery();
        $this->handler->__invoke($query);
    }
}
