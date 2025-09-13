<?php

declare(strict_types=1);

namespace App\Feature\Profile;

final class PointsService
{
    /**
     * @param array<int, int> $starred
     */
    public function calculate(array $starred): int
    {
        // TODO implement logic
        return \count($starred);
    }
}
