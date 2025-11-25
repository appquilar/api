<?php declare(strict_types=1);

namespace App\Tests\Unit\Rent\Application\Query\GetRentById;

use App\Rent\Application\Query\GetRentById\GetRentByIdQuery;
use App\Rent\Application\Query\GetRentById\GetRentByIdQueryHandler;
use App\Rent\Application\Query\GetRentById\GetRentByIdQueryResult;
use App\Rent\Application\Repository\RentRepositoryInterface;
use App\Rent\Application\Transformer\RentTransformer;
use App\Rent\Domain\Entity\Rent;
use App\Rent\Domain\Service\RentAuthorisationServiceInterface;
use App\Shared\Application\Exception\NotFound\EntityNotFoundException;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class GetRentByIdQueryHandlerTest extends UnitTestCase
{
    private RentRepositoryInterface|MockObject $rentRepositoryMock;
    private RentAuthorisationServiceInterface|MockObject $authorizationServiceMock;
    private RentTransformer|MockObject $rentTransformerMock;

    private GetRentByIdQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rentRepositoryMock      = $this->createMock(RentRepositoryInterface::class);
        $this->authorizationServiceMock = $this->createMock(RentAuthorisationServiceInterface::class);
        $this->rentTransformerMock     = $this->createMock(RentTransformer::class);

        $this->handler = new GetRentByIdQueryHandler(
            $this->rentRepositoryMock,
            $this->authorizationServiceMock,
            $this->rentTransformerMock
        );
    }

    public function test_it_returns_transformed_rent_when_found_and_authorised(): void
    {
        $rentId = Uuid::v4();
        $query  = new GetRentByIdQuery($rentId);

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);

        $this->rentRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with($rentId)
            ->willReturn($rent);

        $this->authorizationServiceMock
            ->expects($this->once())
            ->method('canView')
            ->with($rent);

        $transformed = ['foo' => 'bar'];

        $this->rentTransformerMock
            ->expects($this->once())
            ->method('transform')
            ->with($rent)
            ->willReturn($transformed);

        /** @var GetRentByIdQueryResult $result */
        $result = ($this->handler)($query);

        $this->assertInstanceOf(GetRentByIdQueryResult::class, $result);
        $this->assertSame($transformed, $result->getRent());
    }

    public function test_it_throws_entity_not_found_when_rent_is_null(): void
    {
        $rentId = Uuid::v4();
        $query  = new GetRentByIdQuery($rentId);

        $this->rentRepositoryMock
            ->expects($this->once())
            ->method('findById')
            ->with($rentId)
            ->willReturn(null);

        $this->authorizationServiceMock
            ->expects($this->never())
            ->method('canView');

        $this->rentTransformerMock
            ->expects($this->never())
            ->method('transform');

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Entity with id ' . $rentId->toString() . ' not found');

        ($this->handler)($query);
    }
}
