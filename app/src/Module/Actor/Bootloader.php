<?php

declare(strict_types=1);

namespace App\Module\Actor;

use Spiral\Auth\ActorProviderInterface;
use Spiral\Auth\TokenInterface;
use Spiral\Boot\Bootloader\Bootloader as SpiralBootloader;

final class Bootloader extends SpiralBootloader
{
    public function defineSingletons(): array
    {
        return [
            ActorProviderInterface::class => new class implements ActorProviderInterface {
                public function getActor(TokenInterface $token): ?object
                {
                    return new \stdClass();
                }
            },
        ];
    }
}
