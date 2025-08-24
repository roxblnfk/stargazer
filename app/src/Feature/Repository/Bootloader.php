<?php

declare(strict_types=1);

namespace App\Feature\Repository;

use Spiral\Boot\Bootloader\Bootloader as SpiralBootloader;
use Spiral\Views\Bootloader\ViewsBootloader;

final class Bootloader extends SpiralBootloader
{
    public function defineDependencies(): array
    {
        return [
            ViewsBootloader::class,
        ];
    }

    public function boot(ViewsBootloader $views): void
    {
        $views->addDirectory('repository', __DIR__ . '/views');
    }
}
