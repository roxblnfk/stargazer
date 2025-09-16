<?php

declare(strict_types=1);

namespace App\Module\Campaign\Internal\ORM;

use App\Application\ORM\BaseRepository;

/**
 * @extends BaseRepository<CampaignEntity>
 */
final class CampaignRepository extends BaseRepository
{
    public function visible(bool $value = true): static
    {
        $clone = clone $this;
        $clone->select->where(['visible' => $value]);
        return $clone;
    }

    public function invitationCode(string $code): static
    {
        $clone = clone $this;
        $clone->select->where(['inviteCode' => $code]);
        return $clone;
    }
}
