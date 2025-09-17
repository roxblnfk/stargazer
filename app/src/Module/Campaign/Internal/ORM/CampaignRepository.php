<?php

declare(strict_types=1);

namespace App\Module\Campaign\Internal\ORM;

use App\Application\ORM\BaseRepository;

/**
 * @extends BaseRepository<CampaignEntity>
 */
final class CampaignRepository extends BaseRepository
{
    public function forMember(int $userId, bool $joined = true): static
    {
        $clone = clone $this;
        $joined
            ? $clone->select->with('members', ['where' => ['userId' => ['=' => $userId]]])
            : $clone->select->with('members', ['where' => ['@and' => [['userId' => ['!=' => $userId]]]]]);
        return $clone;
    }

    /**
     * Load members of the campaign, optionally filtering by user IDs.
     *
     * @param int[]|null $filterIds Array of user IDs to filter members, or null to load all members.
     * @return static A new instance of the repository with members loaded.
     */
    public function withLoadedMembers(?array $filterIds = null): static
    {
        $clone = clone $this;
        $filterIds === null
            ? $clone->select->load('members')
            : $clone->select->load('members', ['where' => ['userId' => ['in' => $filterIds]]]);
        return $clone;
    }

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
