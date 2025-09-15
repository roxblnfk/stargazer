<?php

declare(strict_types=1);

namespace App\Module\Main\DTO;

final class Dashboard
{
    public function __construct(
        public readonly int $countCampaigns,
        public readonly int $countRepositories,
        public readonly int $countUsers,
        public readonly int $countStars,
    ) {}
}
