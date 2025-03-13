<?php

declare(strict_types=1);

namespace App\Media\Application\Enum;

enum ImageSize: string
{
    case ORIGINAL = 'original';
    case LARGE = 'large';
    case MEDIUM = 'medium';
    case THUMBNAIL = 'thumbnail';
}
