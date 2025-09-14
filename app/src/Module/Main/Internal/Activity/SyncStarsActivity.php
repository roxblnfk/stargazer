<?php

declare(strict_types=1);

namespace App\Module\Main\Internal\Activity;

use App\Application\ORM\ActiveRecord;
use App\Module\Main\Internal\ORM\StarEntity;
use App\Module\Main\Internal\ORM\StarRepository;
use App\Module\Main\Internal\ORM\SyncEntity;
use App\Module\Main\Internal\ORM\SyncStarEntity;
use App\Module\Main\RepositoryService;
use App\Module\Main\UserService;
use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\GithubService;
use Cycle\Database\DatabaseInterface;
use Cycle\ORM\ORMInterface;
use React\Promise\PromiseInterface;
use Spiral\TemporalBridge\Attribute\AssignWorker;
use Temporal\Activity\ActivityInterface;
use Temporal\Support\Attribute\TaskQueue;

#[ActivityInterface(prefix: 'SyncStarsActivity.')]
#[AssignWorker('stargazer-github')]
#[TaskQueue('stargazer-github')]
final class SyncStarsActivity
{
    public function __construct(
        private readonly GithubService $githubService,
        private readonly RepositoryService $repositoryService,
        private readonly DatabaseInterface $db,
        private readonly UserService $userService,
        private readonly StarRepository $starRepository,
        private readonly ORMInterface $orm,
    ) {}

    /**
     * Create new sync state.
     *
     * @return PromiseInterface<positive-int>
     */
    public function createSyncState(GithubRepository $repository): int
    {
        $repo = $this->repositoryService->getRepository($repository);
        $sync = SyncEntity::create($repo->id);
        $sync->saveOrFail();
        return $sync->id;
    }

    /**
     * Complete sync state.
     *
     * @return PromiseInterface<null>
     */
    public function completeSyncState(int $syncId, int $starsCount): void
    {
        $sync = SyncEntity::findByPK($syncId);
        $sync->repoStars = $starsCount;
        $sync->finishedAt = new \DateTimeImmutable();
        $sync->saveOrFail();
    }

    /**
     * Remove temporary sync data.
     *
     * @return PromiseInterface<null>
     */
    public function cleanup(int $syncId): void
    {
        # Cleanup
        $this->db->delete(StarEntity::tableName(), [
            'sync_id' => $syncId,
        ]);
    }

    /**
     * Grab stars from GitHub and store them in temporary storage.
     *
     * @return PromiseInterface<int<0, max>> Number of grabbed stars
     */
    public function grabStars(int $syncId, GithubRepository $repository): int
    {
        # todo get repository from DB by Sync ID
        # Get stargazers iterator from GitHub
        $stargazers = $this->githubService->getStargazers($repository);

        # todo batch insert
        return ActiveRecord::transact(static function () use ($stargazers, $syncId): int {
            # Map stargazers to temporary storage
            return ActiveRecord::groupActions(static function () use ($stargazers, $syncId): int {
                $stars = 0;
                foreach ($stargazers as $stargazer) {
                    $star = SyncStarEntity::create($syncId, $stargazer);
                    $star->save();
                    ++$stars;
                }

                return $stars;
            });
        });
    }

    /**
     * Sync existing stars with the latest data from GitHub.
     *
     * @return PromiseInterface<null>
     */
    public function syncExistingStars(int $syncId, GithubRepository $repository): void
    {
        # todo get repository from DB by Sync ID
        $repo = $this->repositoryService->getRepository($repository);

        ActiveRecord::transact(function () use ($repo, $syncId): void {
            # Sync stars to main storage
            $starsTable = StarEntity::tableName();
            $syncStarsTable = SyncStarEntity::tableName();

            # Update status of existing stars
            $this->db->execute(
                <<<SQL
                    UPDATE $starsTable
                    SET starred_at = CASE
                        WHEN EXISTS(
                            SELECT 1 FROM $syncStarsTable
                            WHERE $syncStarsTable.user_id = $starsTable.user_id
                            AND $syncStarsTable.sync_id = ?
                        ) THEN $starsTable.starred_at
                        ELSE NULL
                    END,
                        last_sync_id = ?
                    WHERE repo_id = ?
                    SQL,
                [
                    $syncId,
                    $syncId,
                    $repo->id,
                ],
            );

            # Remove synced temporary stars
            $this->db->execute(
                <<<SQL
                    DELETE FROM {$syncStarsTable}
                    WHERE sync_id = ? AND user_id IN (SELECT user_id FROM {$starsTable} WHERE repo_id = ? AND starred_at IS NOT NULL)
                    SQL,
                [
                    $syncId,
                    $repo->id,
                ],
            );
        });
    }

    /**
     * Store new stars from temporary storage to main storage.
     *
     * @return PromiseInterface<null>
     */
    public function syncStars(int $syncId, GithubRepository $repository): void
    {
        # todo get repository from DB by Sync ID
        $repoId = $this->repositoryService->getRepository($repository)->id;

        begin:
        $stars = SyncStarEntity::query()->where(['syncId' => $syncId])->limit(10)->fetchAll();
        if ($stars === []) {
            return;
        }

        $stargazers = [];
        foreach ($stars as $star) {
            # Persist user outside of transaction
            $user = $this->userService->getOrCreate($star->info->user);

            # Get or create stargazer entity
            $existing = $this->starRepository->findByPK(['userId' => $user->id, 'repoId' => $repoId]);
            if ($existing !== null) {
                $existing->starredAt = $star->info->starredAt;
                $existing->lastSyncId = $syncId;
            }

            $existing ??= StarEntity::create($user->id, $repoId, $syncId, $star->info->starredAt);
            $stargazers[] = $existing;
        }

        # Batch save
        ActiveRecord::groupActions(static function () use ($stargazers, $stars): void {
            \array_map(static fn(StarEntity $s) => $s->save(), $stargazers);
            \array_map(static fn(SyncStarEntity $s) => $s->delete(), $stars);
        });

        $this->orm->getHeap()->clean();

        goto begin;
    }
}
