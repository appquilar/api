<?php declare(strict_types=1);

namespace App\User\Application\Command\UpdateUserAddress;

use App\Shared\Application\Command\Command;
use App\Shared\Application\Command\CommandHandler;
use App\Shared\Application\Context\UserGranted;
use App\Shared\Application\Exception\BadRequest\BadRequestException;
use App\Shared\Application\Exception\Unauthorized\NotEnoughPermissionsException;
use App\Shared\Domain\ValueObject\Address;
use App\Shared\Domain\ValueObject\GeoLocation;
use App\User\Domain\Entity\User;
use App\User\Infrastructure\Persistence\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: UpdateUserAddressCommand::class)]
class UpdateUserAddressCommandHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserGranted $userGranted,
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws NotEnoughPermissionsException
     */
    public function __invoke(Command|UpdateUserAddressCommand $command): void
    {
        /** @var User $user */
        $user = $this->userRepository->findById($command->getUserId());

        if (!$user) {
            throw new BadRequestException('User not found.');
        }

        if (
            !$this->userGranted->isAdmin() &&
            $this->userGranted->getUser()->getId() !== $user->getId()
        ) {
            throw new NotEnoughPermissionsException();
        }

        $user->setAddress($command->getAddress());
        $user->setGeoLocation($command->getGeoLocation());

        $this->userRepository->save($user);
    }
}
