<?php

declare(strict_types=1);

namespace App\Module\Stargazer\Internal;

use App\Module\ORM\BaseRepository;

/**
 * @extends BaseRepository<StarEntity>
 */
final class StarRepository extends BaseRepository
{
    public function whereSyncId(string|\Stringable $id): static
    {
        $clone = clone $this;
        $clone->select->where(['syncId' => $id]);
        return $clone;
    }

    /**
     * @return $this
     */
    public function active(bool $value = true): static
    {
        $clone = clone $this;
        $clone->select->where(['active' => $value]);
        return $clone;
    }
}
