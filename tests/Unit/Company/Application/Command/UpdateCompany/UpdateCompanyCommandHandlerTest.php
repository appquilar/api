<?php

declare(strict_types=1);

namespace App\Tests\Unit\Company\Application\Command\UpdateCompany;

use App\Company\Application\Command\UpdateCompany\UpdateCompanyCommand;
use App\Company\Application\Command\UpdateCompany\UpdateCompanyCommandHandler;
use App\Company\Application\Repository\CompanyRepositoryInterface;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Shared\Application\Exception\Unauthorized\UnauthorizedException;
use App\Shared\Application\Service\SlugifyServiceInterface;
use App\Shared\Domain\ValueObject\PhoneNumber;
use App\Tests\Factories\Company\Domain\Entity\CompanyFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class UpdateCompanyCommandHandlerTest extends UnitTestCase
{
    private CompanyRepositoryInterface|MockObject $companyRepositoryMock;
    private SlugifyServiceInterface|MockObject $slugifyServiceMock;
    private UserGranted|MockObject $userGrantedMock;
    private UpdateCompanyCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyRepositoryMock = $this->createMock(CompanyRepositoryInterface::class);
        $this->slugifyServiceMock = $this->createMock(SlugifyServiceInterface::class);
        $this->userGrantedMock = $this->createMock(UserGranted::class);
        $this->handler = new UpdateCompanyCommandHandler(
            $this->companyRepositoryMock,
            $this->slugifyServiceMock,
            $this->userGrantedMock
        );
    }

    public function testUnauthorizedExceptionIfNotAdminNorAdminAtCompany(): void
    {
        $companyId = Uuid::v4();

        $command = new UpdateCompanyCommand(
            $companyId,
            'new name',
            'new-slug',
            'new description',
            'new fiscal identifier',
            'new address',
            'new postal code',
            'new city',
            'new contact email',
            new PhoneNumber('ES', '+34', '666000000')
        );

        $this->userGrantedMock->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->userGrantedMock->expects($this->once())
            ->method('isAdminAtThisCompany')
            ->with($companyId)
            ->willReturn(false);

        $this->companyRepositoryMock->expects($this->never())
            ->method('save');

        $this->expectException(UnauthorizedException::class);
        $this->handler->__invoke($command);
    }
    public function testUpdateCompanySuccessfully(): void
    {
        $companyId = Uuid::v4();
        $slug = 'new-slug';
        $company = CompanyFactory::createOne(['companyId' => $companyId]);
        $command = new UpdateCompanyCommand(
            $companyId,
            'new name',
            'new-slug',
            'new description',
            'new fiscal identifier',
            'new address',
            'new postal code',
            'new city',
            'new contact email',
            new PhoneNumber('ES', '+34', '666000000')
        );

        $this->userGrantedMock->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->userGrantedMock->expects($this->once())
            ->method('isAdminAtThisCompany')
            ->with($companyId)
            ->willReturn(true);

        $this->companyRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($companyId)
            ->willReturn($company);

        $this->slugifyServiceMock->expects($this->once())
            ->method('validateSlugIsUnique')
            ->with($slug);

        $this->slugifyServiceMock->expects($this->once())
            ->method('generate')
            ->with($slug)
            ->willReturn($slug);
        $this->companyRepositoryMock->expects($this->once())
            ->method('save');

        $this->handler->__invoke($command);
    }

    public function testNotFindingCompanyById(): void
    {
        $companyId = Uuid::v4();
        $command = new UpdateCompanyCommand(
            $companyId,
            'new name',
            'new-slug',
            'new description',
            'new fiscal identifier',
            'new address',
            'new postal code',
            'new city',
            'new contact email',
            new PhoneNumber('ES', '+34', '666000000')
        );

        $this->userGrantedMock->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->userGrantedMock->expects($this->once())
            ->method('isAdminAtThisCompany')
            ->with($companyId)
            ->willReturn(true);

        $this->companyRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($companyId)
            ->willReturn(null);

        $this->companyRepositoryMock->expects($this->never())
            ->method('save');

        $this->expectException(EntityNotFoundException::class);
        $this->handler->__invoke($command);
    }

    public function testSlugValidationThrowsException(): void
    {
        $companyId = Uuid::v4();
        $slug = 'new-slug';
        $company = CompanyFactory::createOne(['companyId' => $companyId]);
        $command = new UpdateCompanyCommand(
            $companyId,
            'new name',
            'new-slug',
            'new description',
            'new fiscal identifier',
            'new address',
            'new postal code',
            'new city',
            'new contact email',
            new PhoneNumber('ES', '+34', '666000000')
        );

        $this->userGrantedMock->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->userGrantedMock->expects($this->once())
            ->method('isAdminAtThisCompany')
            ->with($companyId)
            ->willReturn(true);

        $this->companyRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($companyId)
            ->willReturn($company);

        $this->slugifyServiceMock->expects($this->once())
            ->method('validateSlugIsUnique')
            ->with($slug)
            ->willThrowException(new BadRequestException());

        $this->companyRepositoryMock->expects($this->never())
            ->method('save');

        $this->expectException(BadRequestException::class);
        $this->handler->__invoke($command);
    }
}
