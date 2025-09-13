<?php

declare(strict_types=1);

namespace App\Module\Repository\Internal\Activity;

use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\GithubService;
use App\Module\Github\Result\RepositoryInfo;
use App\Module\ORM\ActiveRecord;
use App\Module\Repository\Internal\ORM\RepoEntity;
use App\Module\Repository\Internal\ORM\RepoRepository;
use React\Promise\PromiseInterface;
use Spiral\TemporalBridge\Attribute\AssignWorker;
use Temporal\Activity\ActivityInterface;
use Temporal\Support\Attribute\TaskQueue;

#[ActivityInterface(prefix: 'RepositoryActivity.')]
#[AssignWorker('stargazer-github')]
#[TaskQueue('stargazer-github')]
class RepositoryActivity
{
    public function __construct(
        private readonly RepoRepository $repoRepository,
        private readonly GithubService $githubService,
    ) {}

    /**
     * @return PromiseInterface<RepositoryInfo>
     */
    public function getGithubInfo(GithubRepository $repository): RepositoryInfo
    {
        # Load info from GitHub
        return $this->githubService->getRepositoryInfo($repository);
    }

    /**
     * @return PromiseInterface<null>
     */
    public function createOrUpdate(GithubRepository $repository, RepositoryInfo $info): void
    {
        ActiveRecord::transact(function () use ($info, $repository): void {
            $repo = $this->repoRepository->forUpdate()->whereFullName($repository)->findOne();

            # Create if not found or update info
            $repo === null
                ? $repo = RepoEntity::createFromRepositoryInfo($info)
                : $repo->info = $info;

            # Persist info
            $repo->saveOrFail();
        });
    }
}
