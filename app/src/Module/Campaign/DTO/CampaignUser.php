<?php

declare(strict_types=1);

namespace App\Module\Campaign\DTO;

use App\Module\Github\Dto\GithubUser;
use Ramsey\Uuid\UuidInterface;

final class CampaignUser implements \Stringable
{
    public function __construct(
        public readonly UuidInterface $campaignUuid,
        public readonly int $userId,
        public readonly GithubUser $userName,
        public readonly int $score,
        public readonly \DateTimeInterface $updatedAt,
        public readonly \DateTimeInterface $createdAt,
    ) {}

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return (string) $this->userName;
    }
}
