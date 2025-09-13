<?php

declare(strict_types=1);

namespace App\Module\Repository\Internal;

use App\Module\Github\Dto\GithubRepository;
use App\Module\Repository\Internal\Activity\SyncStarsActivity;
use Spiral\TemporalBridge\Attribute\AssignWorker;
use Temporal\Promise;
use Temporal\Support\Attribute\TaskQueue;
use Temporal\Support\Factory\ActivityStub;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
#[AssignWorker('stargazer-github')]
#[TaskQueue('stargazer-github')]
final class SyncStarsWorkflow
{
    #[WorkflowMethod]
    public function handle(GithubRepository $repository)
    {
        $starsCount = 0;
        $syncId = yield ActivityStub::activity(SyncStarsActivity::class, retryAttempts: 1, startToCloseTimeout: 2)
            ->createSyncState($repository);

        try {
            # Grab stars
            $starsCount = yield ActivityStub::activity(
                SyncStarsActivity::class,
                retryAttempts: 2,
                startToCloseTimeout: 10,
            )->grabStars($syncId, $repository);

            # Sync existing stars
            yield ActivityStub::activity(SyncStarsActivity::class, retryAttempts: 2, startToCloseTimeout: 10)
                ->syncExistingStars($syncId, $repository);

            # Sync new stars
            yield ActivityStub::activity(SyncStarsActivity::class, retryAttempts: 2, startToCloseTimeout: 10)
                ->syncStars($syncId, $repository);
        } finally {
            # Complete sync state and cleanup in parallel
            yield Workflow::asyncDetached(static function () use ($syncId, $starsCount) {
                yield Promise::all([
                    ActivityStub::activity(SyncStarsActivity::class, retryAttempts: 3, startToCloseTimeout: 10)
                        ->completeSyncState($syncId, $starsCount),
                    ActivityStub::activity(SyncStarsActivity::class, retryAttempts: 10, startToCloseTimeout: 60)
                        ->cleanup($syncId),
                ]);
            });
        }
    }
}
