<?php

declare(strict_types=1);

namespace App\Module\Campaign\DTO;

use App\Module\Github\Dto\GithubRepository;
use Ramsey\Uuid\UuidInterface;

final class CampaignRepo implements \Stringable
{
    public function __construct(
        public readonly UuidInterface $campaignUuid,
        public readonly int $repoId,
        public readonly GithubRepository $repoName,
        public readonly int $score,
        public readonly \DateTimeInterface $updatedAt,
        public readonly \DateTimeInterface $createdAt,
    ) {}

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return (string) $this->repoName;
    }
}
