<?php

declare(strict_types=1);

namespace App\Module\Stargazer\Internal;

use App\Module\ORM\BaseRepository;

/**
 * @extends BaseRepository<StarEntity>
 */
final class StarRepository extends BaseRepository
{
    /**
     * @return $this
     */
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
        $clone->select->where('starredAt', $value ? '!=' : '=', null);
        return $clone;
    }

    /**
     * @return $this
     */
    public function whereUserId(int $id): static
    {
        $clone = clone $this;
        $clone->select->where(['userId' => $id]);
        return $clone;
    }

    /**
     * @return $this
     */
    public function whereRepoId(int $id): static
    {
        $clone = clone $this;
        $clone->select->where(['repoId' => $id]);
        return $clone;
    }

    /**
     * Get star count data grouped by date for charts
     * @return array{date: string, count: int}[]
     */
    public function getStarCountsByDate(int $repoId): array
    {
        $clone = $this
            ->whereRepoId($repoId)
            ->active();

        $clone->select->orderBy('starredAt', 'ASC');
        $stars = $clone->findAll();
        $result = [];
        $totalCount = 0;

        foreach ($stars as $star) {
            $date = $star->starredAt->format('Y-m-d');
            if (!isset($result[$date])) {
                $result[$date] = 0;
            }
            $result[$date]++;
        }

        $chartData = [];
        $totalCount = 0;

        // Add data points for each date with stars
        foreach ($result as $date => $count) {
            $totalCount += $count;
            $chartData[] = [
                'date' => $date,
                'count' => $totalCount,
            ];
        }

        // Extend chart to today if last star is older than today
        if (!empty($chartData)) {
            $lastDate = end($chartData)['date'];
            $today = (new \DateTime())->format('Y-m-d');

            if ($lastDate < $today) {
                $chartData[] = [
                    'date' => $today,
                    'count' => $totalCount,
                ];
            }
        }

        return $chartData;
    }
}
