<?php

declare(strict_types=1);

namespace App\Module\Main\Internal\ORM;

use App\Application\ORM\BaseRepository;
use Cycle\Database\Injection\Fragment;

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
        $query = $this
            ->whereRepoId($repoId)
            ->select
            ->buildQuery()
            ->groupBy(new Fragment('DATE(starred_at)'))
            // Summarize the count of stars per day
            ->columns(new Fragment('DATE(starred_at) AS date'), new Fragment('COUNT(*) AS count'))
            ->orderBy('date', 'ASC');

        $chartData = $query->fetchAll();

        // Summarize cumulative counts
        $previous = null;
        foreach ($chartData as &$data) {
            if ($previous === null) {
                $previous = $data;
                continue;
            }

            $data['count'] += $previous['count'];
            $previous = $data;
        }

        // Extend chart to today if last star is older than today
        if ($chartData !== []) {
            $last = \end($chartData);
            $lastDate = $last['date'];
            $today = (new \DateTime())->format('Y-m-d');

            if ($lastDate < $today) {
                $chartData[] = [
                    'date' => $today,
                    'count' => $last['count'],
                ];
            }
        }

        return $chartData;
    }
}
