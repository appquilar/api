<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class PublicationStatus
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    private const VALID_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
        self::STATUS_ARCHIVED
    ];

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $publishedAt;

    public function __construct(string $status = self::STATUS_DRAFT, ?\DateTime $publishedAt = null)
    {
        if (!in_array($status, self::VALID_STATUSES)) {
            throw new \InvalidArgumentException("Invalid publication status: $status");
        }

        $this->status = $status;
        $this->publishedAt = $publishedAt;

        if ($status === self::STATUS_PUBLISHED && $publishedAt === null) {
            $this->publishedAt = new \DateTime();
        }
    }

    public static function default(): self
    {
        return new self(self::STATUS_DRAFT);
    }

    public static function published(): self
    {
        return new self(self::STATUS_PUBLISHED);
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    public function publish(): self
    {
        return new self(self::STATUS_PUBLISHED, new \DateTime());
    }

    public function unpublish(): self
    {
        return new self(self::STATUS_DRAFT, null);
    }

    public function archive(): self
    {
        return new self(self::STATUS_ARCHIVED, $this->publishedAt);
    }
}