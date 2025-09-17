<?php

declare(strict_types=1);

namespace App\Module\Main\Internal\ORM;

use App\Application\ORM\BaseRepository;

/**
 * @extends BaseRepository<RepoEntity>
 */
final class RepoRepository extends BaseRepository
{
    public function whereFullName(string|\Stringable $fullName): static
    {
        $parts = \explode('/', (string) $fullName, 2);
        \count($parts) === 2 or throw new \InvalidArgumentException(
            'Invalid repository full name format. Expected "owner/name".',
        );
        [$owner, $name] = $parts;

        $clone = clone $this;
        $clone->select->where(['owner' => $owner, 'name' => $name]);
        return $clone;
    }

    /**
     * Exclude repositories by their IDs.
     *
     * @param int[] $ids Array of repository IDs to exclude.
     * @return static A new instance of the repository with the applied exclusion filter.
     */
    public function exclude(array $ids): static
    {
        $clone = clone $this;
        $clone->select->where('id', 'not in', $ids);
        return $clone;
    }

    public function active(bool $value = true): static
    {
        $clone = clone $this;
        $clone->select->where(['active' => $value]);
        return $clone;
    }
}
