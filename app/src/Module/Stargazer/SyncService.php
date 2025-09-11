<?php

declare(strict_types=1);

namespace App\Module\Stargazer;

use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\GithubService;
use App\Module\ORM\ActiveRecord;
use App\Module\Repository\RepositoryService;
use App\Module\Stargazer\Internal\StarEntity;
use App\Module\Stargazer\Internal\SyncEntity;
use App\Module\Stargazer\Internal\SyncStarEntity;
use App\Module\User\UserService;
use Cycle\Database\DatabaseInterface;

class SyncService
{
    public function __construct(
        private readonly GithubService $githubService,
        private readonly RepositoryService $repositoryService,
        private readonly DatabaseInterface $db,
        private readonly UserService $userService,
    ) {}

    public function syncStars(GithubRepository $repository): void
    {
        $repo = $this->repositoryService->getTrackedRepository($repository);
        ActiveRecord::transact(function () use ($repo, $repository): void {
            # Create Sync state
            $sync = SyncEntity::create($repo->id);
            $sync->saveOrFail();

            // ---

            # Get stargazers iterator from GitHub
            $stargazers = $this->githubService->getStargazers($repository);

            # Map stargazers to temporary storage
            $starsCount = ActiveRecord::groupActions(static function () use ($stargazers, $sync): int {
                $stars = 0;
                foreach ($stargazers as $stargazer) {
                    $star = SyncStarEntity::create($sync->id, $stargazer);
                    $star->save();
                    ++$stars;
                }

                return $stars;
            });


            // ---

            # Sync stars to main storage
            $starsTable = StarEntity::getTableName();
            $syncStarsTable = SyncStarEntity::getTableName();

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
                    $sync->id,
                    $sync->id,
                    $repo->id,
                ],
            );

            # Remove synced temporary stars
            $this->db->execute(
                <<<SQL
                DELETE FROM {$syncStarsTable}
                WHERE sync_id = ? AND user_id IN (SELECT user_id FROM {$starsTable} WHERE repo_id = ?)
                SQL,
                [
                    $repo->id,
                    $sync->id,
                ],
            );

            // ---

            # Create new stars and users
            ActiveRecord::groupActions(function () use ($starsTable, $syncStarsTable, $sync, $repo): void {
                $stars = SyncStarEntity::query()->where('syncId', $sync->id)->fetchAll();
                foreach ($stars as $star) {
                    $user = $this->userService->getOrCreate($star->info->user);
                    $existing = StarEntity::create($user->id, $repo->id, $sync->id, $star->info->starredAt);
                    $existing->saveOrFail();
                }
            });

            // ---

            # Cleanup
            $this->db->delete(StarEntity::getTableName(), [
                'sync_id' => $sync->id,
            ]);

            $sync->repoStars = $starsCount;
            $sync->finishedAt = new \DateTimeImmutable();
            $sync->save();
        });
    }
}
