<?php declare(strict_types=1);

namespace App\Tests\Unit\Product\Domain\ValueObject;

use App\Product\Domain\ValueObject\PublicationStatus;
use App\Tests\Unit\UnitTestCase;

class PublicationStatusTest extends UnitTestCase
{
    public function test_default_is_draft_without_published_at(): void
    {
        $status = PublicationStatus::default();

        $this->assertTrue($status->isDraft());
        $this->assertFalse($status->isPublished());
        $this->assertFalse($status->isArchived());
        $this->assertSame(PublicationStatus::STATUS_DRAFT, $status->getStatus());
        $this->assertNull($status->getPublishedAt());
    }

    public function test_published_creates_published_status_with_published_at(): void
    {
        $status = PublicationStatus::published();

        $this->assertTrue($status->isPublished());
        $this->assertFalse($status->isDraft());
        $this->assertFalse($status->isArchived());
        $this->assertSame(PublicationStatus::STATUS_PUBLISHED, $status->getStatus());
        $this->assertInstanceOf(\DateTime::class, $status->getPublishedAt());
    }

    public function test_constructor_with_invalid_status_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid publication status: invalid');

        new PublicationStatus('invalid');
    }

    public function test_constructor_with_published_status_and_explicit_datetime_keeps_that_published_at(): void
    {
        $dateTime = new \DateTime('2025-01-01 12:00:00');

        $status = new PublicationStatus(PublicationStatus::STATUS_PUBLISHED, $dateTime);

        $this->assertTrue($status->isPublished());
        $this->assertSame($dateTime, $status->getPublishedAt());
    }

    public function test_is_archived_and_archive(): void
    {
        $dateTime = new \DateTime('2025-01-01 12:00:00');
        $published = new PublicationStatus(PublicationStatus::STATUS_PUBLISHED, $dateTime);

        $archived = $published->archive();

        $this->assertTrue($archived->isArchived());
        $this->assertFalse($archived->isDraft());
        $this->assertFalse($archived->isPublished());
        $this->assertSame(PublicationStatus::STATUS_ARCHIVED, $archived->getStatus());
        // archive() conserva el publishedAt anterior
        $this->assertSame($dateTime, $archived->getPublishedAt());
    }

    public function test_archive_from_draft_has_null_published_at(): void
    {
        $draft = PublicationStatus::default();

        $archived = $draft->archive();

        $this->assertTrue($archived->isArchived());
        $this->assertNull($archived->getPublishedAt());
    }

    public function test_publish_creates_new_published_instance_with_published_at(): void
    {
        $draft = PublicationStatus::default();

        $published = $draft->publish();

        $this->assertTrue($published->isPublished());
        $this->assertFalse($published->isDraft());
        $this->assertInstanceOf(\DateTime::class, $published->getPublishedAt());

        // El original sigue siendo draft
        $this->assertTrue($draft->isDraft());
    }

    public function test_unpublish_creates_new_draft_without_published_at(): void
    {
        $published = PublicationStatus::published();

        $draftAgain = $published->unpublish();

        $this->assertTrue($draftAgain->isDraft());
        $this->assertNull($draftAgain->getPublishedAt());

        // El original sigue siendo published
        $this->assertTrue($published->isPublished());
        $this->assertNotNull($published->getPublishedAt());
    }
}
