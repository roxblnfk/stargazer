<?php

declare(strict_types=1);

namespace App\Module\Campaign\Internal\ORM;

use App\Application\ORM\BaseRepository;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends BaseRepository<CampaignUserEntity>
 */
final class CampaignUserRepository extends BaseRepository
{
    public function withCampaignUuid(UuidInterface $uuid): static
    {
        $clone = clone $this;
        $clone->select->where(['campaignUuid' => $uuid]);
        return $clone;
    }

    public function withUserId(int $id): static
    {
        $clone = clone $this;
        $clone->select->where(['userId' => $id]);
        return $clone;
    }

    public function sortByScore($direction = 'DESC'): static
    {
        $clone = clone $this;
        $clone->select->orderBy(['score' => $direction]);
        return $clone;
    }
}
