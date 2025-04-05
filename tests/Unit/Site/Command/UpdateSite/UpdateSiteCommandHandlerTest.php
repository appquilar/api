<?php

declare(strict_types=1);

namespace App\Tests\Unit\Site\Command\UpdateSite;

use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Site\Application\Command\UpdateSite\UpdateSiteCommand;
use App\Site\Application\Command\UpdateSite\UpdateSiteCommandHandler;
use App\Site\Application\Repository\SiteRepositoryInterface;
use App\Tests\Factories\Site\Domain\Entity\SiteFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class UpdateSiteCommandHandlerTest extends UnitTestCase
{
    private SiteRepositoryInterface|MockObject $siteRepositoryMock;
    private UpdateSiteCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->siteRepositoryMock = $this->createMock(SiteRepositoryInterface::class);
        $this->handler = new UpdateSiteCommandHandler($this->siteRepositoryMock);
    }

    public function testUpdateSiteSuccessful(): void
    {
        $siteId = Uuid::v4();
        $site = SiteFactory::createOne(['siteId' => $siteId]);

        $this->siteRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($siteId)
            ->willReturn($site);

        $this->siteRepositoryMock->expects($this->once())
            ->method('save');

        $command = new UpdateSiteCommand($siteId, 'name', 'title', 'url', 'description', Uuid::v4(), Uuid::v4(), '128aaa', [Uuid::v4()], [Uuid::v4()], [Uuid::v4()]);
        $this->handler->__invoke($command);
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

        $command = new UpdateSiteCommand($siteId, 'name', 'title', 'url', 'description', Uuid::v4(), Uuid::v4(), '128aaa', [Uuid::v4()], [Uuid::v4()], [Uuid::v4()]);
        $this->handler->__invoke($command);
    }
}
