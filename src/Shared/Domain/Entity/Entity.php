<?php

declare(strict_types=1);

namespace App\Shared\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class Entity
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;
    #[ORM\Column(type: 'datetime_microseconds')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime_microseconds', nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function __construct(Uuid $id)
    {
        $this->id = $id;
        $this->createdAt = new \DateTime();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
