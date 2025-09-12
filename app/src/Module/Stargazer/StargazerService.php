<?php

declare(strict_types=1);

namespace App\Module\Stargazer;

use App\Module\Stargazer\Internal\StarRepository;

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

        return array_combine(
            array_map(static fn($star) => $star->repoId, $stars),
            array_map(static fn($star) => $star->repoId, $stars)
        );
    }
}
