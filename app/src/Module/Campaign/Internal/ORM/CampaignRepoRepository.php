<?php

declare(strict_types=1);

namespace App\Module\Campaign\Internal\ORM;

use App\Application\ORM\BaseRepository;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends BaseRepository<CampaignRepoEntity>
 */
final class CampaignRepoRepository extends BaseRepository
{
    public function withCampaignUuid(UuidInterface $uuid): static
    {
        $clone = clone $this;
        $clone->select->where(['campaignUuid' => $uuid]);
        return $clone;
    }

    public function withRepoId(int $id): static
    {
        $clone = clone $this;
        $clone->select->where(['repoId' => $id]);
        return $clone;
    }
}
