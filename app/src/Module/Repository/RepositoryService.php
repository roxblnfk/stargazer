<?php

declare(strict_types=1);

namespace App\Module\Repository;

use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\GithubService;
use App\Module\Github\Result\RepositoryInfo;
use App\Module\Repository\Exception\RepositoryAlreadyExists;
use App\Module\Repository\Internal\RepoEntity;
use App\Module\Repository\Internal\RepoRepository;
use Spiral\Core\Attribute\Singleton;
use Spiral\Prototype\Traits\PrototypeTrait;

#[Singleton]
class RepositoryService
{
    use PrototypeTrait;

    public function __construct(
        private readonly RepoRepository $repoRepository,
        private readonly GithubService $githubService,
    ) {}

    /**
     * @return \Iterator<int, GithubRepository>
     */
    public function getTrackedRepositories(): \Iterator
    {
        foreach ($this->repoRepository->active()->findAll() as $repo) {
            yield $repo->toGithubRepository();
        }
    }

    /**
     * @throws RepositoryAlreadyExists
     */
    public function registerRepository(GithubRepository $repository): void
    {
        $found = $this->repoRepository->whereFullName($repository)->findOne();
        $found === null or throw new RepositoryAlreadyExists($repository);

        # Load info from GitHub
        $info = $this->githubService->getRepositoryInfo($repository);

        # Create entity
        $repo = RepoEntity::createFromRepositoryInfo($info);
        $repo->saveOrFail();

        # Harvest stars
        // $this->workflowClient->
    }

    public function getTrackedRepository(GithubRepository $repository): RepositoryInfo
    {
        return $this->repoRepository
            ->active()
            ->whereFullName($repository)
            ->findOne()?->info ?? throw new \RuntimeException('Repository is not tracked.');
    }
}
