<?php

declare(strict_types=1);

namespace Sifo\Test\Sifo;

use Sifo\Images;

final class TestImages extends Images
{
    protected static function moveFile(string $from, string $to): void
    {
        rename($from, $to);
    }
}
    