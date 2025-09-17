<?php

declare(strict_types=1);

namespace App\Module\Campaign\DTO;

/**
 * A Repository in scope of a specific user of the specific campaign.
 */
final class UserRepositoryDetails
{
    public function __construct(
        public readonly CampaignRepo $campaignRepo,
        public readonly \App\Module\Main\DTO\Repository $repository,
        public readonly bool $starred,
    ) {}
}
