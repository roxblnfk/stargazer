<?php

declare(strict_types=1);

namespace App\Module\Campaign\DTO;

/**
 * A Campaign in scope of a specific user.
 */
final class UserCampaign
{
    public readonly bool $finished;

    public function __construct(
        public readonly Campaign $campaign,
        /** Null if the user is not a member of the campaign */
        public readonly ?CampaignUser $user,
    ) {
        $this->finished = $campaign->finishedAt !== null && $campaign->finishedAt <= new \DateTimeImmutable();
    }
}
