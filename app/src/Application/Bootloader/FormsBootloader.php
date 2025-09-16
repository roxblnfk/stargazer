<?php

declare(strict_types=1);

namespace App\Application\Bootloader;

use App\Application\Form\DateTimeCaster;
use Spiral\Boot\Bootloader\Bootloader as BaseBootloader;
use Spiral\Filters\Model\Mapper\CasterRegistryInterface;
use Spiral\Filters\Model\Mapper\EnumCaster;

final class FormsBootloader extends BaseBootloader
{
    public function boot(CasterRegistryInterface $casterRegistry): void
    {
        $casterRegistry->register(new EnumCaster());
        $casterRegistry->register(new DateTimeCaster());
    }
}
