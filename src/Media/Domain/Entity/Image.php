<?php

declare(strict_types=1);

namespace App\Media\Domain\Entity;

use App\Shared\Domain\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: "media")]
#[ORM\Index(name: "media_filename_idx", columns: ["original_filename"])]
class Image extends Entity
{
    #[ORM\Column(type: "string", length: 255)]
    private string $originalFilename;

    #[ORM\Column(type: "string", length: 255)]
    private string $mimeType;

    #[ORM\Column(type: "integer")]
    private int $size;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $width;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $height;

    public function __construct(
        Uuid   $imageId,
        string $originalFilename,
        string $mimeType,
        int    $size,
        ?int   $width = null,
        ?int   $height = null,
    )
    {
        parent::__construct($imageId);

        $this->originalFilename = $originalFilename;
        $this->mimeType = $mimeType;
        $this->size = $size;
        $this->width = $width;
        $this->height = $height;
    }
}
