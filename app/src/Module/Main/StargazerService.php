<?php

declare(strict_types=1);

namespace App\Module\Main;

use App\Module\Main\Internal\ORM\StarRepository;

final class StargazerService
{
    public function __construct(
        private readonly StarRepository $starRepository,
    ) {}

    /**
     * @return array<int, int>
     */
    public function getRepositoryIdsByUserId(int $userId): array
    {
        $stars = $this->starRepository->whereUserId($userId)->active()->findAll();

        return \array_combine(
            \array_map(static fn($star) => $star->repoId, $stars),
            \array_map(static fn($star) => $star->repoId, $stars),
        );
    }

    /**
     * Get chart data for repository stars over time
     * @return array{labels: string[], data: int[]}
     */
    public function getRepositoryStarChartData(int $repoId): array
    {
        $chartData = $this->starRepository->getStarCountsByDate($repoId);

        return [
            'labels' => \array_map(static fn($item) => $item['date'], $chartData),
            'data' => \array_map(static fn($item) => $item['count'], $chartData),
        ];
    }
}
