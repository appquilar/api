<?php declare(strict_types=1);

namespace App\Tests\Unit\Rent\Application\Query\ListRents;

use App\Rent\Application\Query\ListRents\ListRentsQuery;
use App\Rent\Application\Query\ListRents\ListRentsQueryHandler;
use App\Rent\Application\Query\ListRents\ListRentsQueryResult;
use App\Rent\Application\Repository\RentRepositoryInterface;
use App\Rent\Application\Transformer\RentTransformer;
use App\Rent\Domain\Entity\Rent;
use App\Shared\Application\Context\UserGranted;
use App\Tests\Factories\User\Domain\Entity\UserFactory;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class ListRentsQueryHandlerTest extends UnitTestCase
{
    private RentRepositoryInterface|MockObject $rentRepositoryMock;
    private RentTransformer|MockObject $rentTransformerMock;
    private UserGranted|MockObject $userGrantedMock;

    private ListRentsQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rentRepositoryMock   = $this->createMock(RentRepositoryInterface::class);
        $this->rentTransformerMock  = $this->createMock(RentTransformer::class);
        $this->userGrantedMock      = $this->createMock(UserGranted::class);

        $this->handler = new ListRentsQueryHandler(
            $this->rentRepositoryMock,
            $this->rentTransformerMock,
            $this->userGrantedMock
        );
    }

    public function test_non_admin_uses_logged_in_user_id_and_transforms_results(): void
    {
        $loggedInOwnerId = Uuid::v4();
        $productId       = Uuid::v4();

        $query = new ListRentsQuery(
            productId: $productId,
            startDate: null,
            endDate: null,
            status: null,
            ownerId: null,
            page: 2,
            perPage: 5
        );

        $user = UserFactory::createOne(['userId' => $loggedInOwnerId]);

        $this->userGrantedMock
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $this->userGrantedMock
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        /** @var Rent|MockObject $rent1 */
        $rent1 = $this->createMock(Rent::class);
        /** @var Rent|MockObject $rent2 */
        $rent2 = $this->createMock(Rent::class);

        $this->rentRepositoryMock
            ->expects($this->once())
            ->method('searchByOwner')
            ->with(
                $this->callback(fn (Uuid $ownerId): bool => $ownerId->equals($loggedInOwnerId)),
                $productId,
                null,
                null,
                null,
                2,
                5
            )
            ->willReturn([
                'items' => [$rent1, $rent2],
                'total' => 2,
            ]);

        $this->rentTransformerMock
            ->expects($this->exactly(2))
            ->method('transform')
            ->withConsecutive([$rent1], [$rent2])
            ->willReturnOnConsecutiveCalls(
                ['rent_id' => '1'],
                ['rent_id' => '2']
            );

        $result = ($this->handler)($query);

        $this->assertInstanceOf(ListRentsQueryResult::class, $result);
        // No asumimos la estructura interna de ListRentsQueryResult, sólo que no explota y se transforma
    }

    public function test_admin_with_owner_id_uses_owner_id_from_query(): void
    {
        $adminId         = Uuid::v4();
        $ownerFilterId   = Uuid::v4();
        $productId       = Uuid::v4();

        $query = new ListRentsQuery(
            productId: $productId,
            startDate: null,
            endDate: null,
            status: null,
            ownerId: $ownerFilterId,
            page: 1,
            perPage: 10
        );

        $user = new class($adminId) {
            public function __construct(private Uuid $id) {}
            public function getId(): Uuid
            {
                return $this->id;
            }
        };

        $this->userGrantedMock
            ->expects($this->once())
            ->method('isAdmin')
            ->willReturn(true);

        // En este caso NO debería llamar a getUser(), porque usa ownerId del query
        $this->userGrantedMock
            ->expects($this->never())
            ->method('getUser');

        /** @var Rent|MockObject $rent */
        $rent = $this->createMock(Rent::class);

        $this->rentRepositoryMock
            ->expects($this->once())
            ->method('searchByOwner')
            ->with(
                $this->callback(fn (Uuid $ownerId): bool => $ownerId->equals($ownerFilterId)),
                $productId,
                null,
                null,
                null,
                1,
                10
            )
            ->willReturn([
                'items' => [$rent],
                'total' => 1,
            ]);

        $this->rentTransformerMock
            ->expects($this->once())
            ->method('transform')
            ->with($rent)
            ->willReturn(['rent_id' => '1']);

        $result = ($this->handler)($query);

        $this->assertInstanceOf(ListRentsQueryResult::class, $result);
    }
}
