<?php

declare(strict_types=1);

namespace App\Module\Repository;

use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\Result\RepositoryInfo;
use App\Module\Repository\Internal\ORM\RepoRepository;
use App\Module\Repository\Internal\RepositoryWorkflow;
use Spiral\Core\Attribute\Singleton;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Common\IdReusePolicy;
use Temporal\Common\WorkflowIdConflictPolicy;
use Temporal\Exception\Client\WorkflowNotFoundException;
use Temporal\Support\Factory\WorkflowStub;

#[Singleton]
class RepositoryService
{
    public function __construct(
        private readonly RepoRepository $repoRepository,
        private readonly WorkflowClientInterface $workflowClient,
    ) {}

    /**
     * @return \Iterator<int, GithubRepository>
     */
    public function getRepositories(?bool $active = null): \Iterator
    {
        $q = $this->repoRepository;
        $active === null or $q = $q->active($active);
        foreach ($q->findAll() as $repo) {
            yield $repo->toGithubRepository();
        }
    }

    /**
     * @return \Iterator<int, RepositoryInfo>
     */
    public function getTrackedRepositoriesInfo(): iterable
    {
        foreach ($this->repoRepository->active()->findAll() as $repo) {
            yield $repo->info;
        }
    }

    public function getRepository(GithubRepository $repository): RepositoryInfo
    {
        return $this->repoRepository
            ->whereFullName($repository)
            ->findOne()?->info ?? throw new \RuntimeException('Repository for found.');
    }

    public function activateRepository(GithubRepository $repository): void
    {
        $stub = WorkflowStub::workflow(
            $this->workflowClient->withTimeout(10),
            RepositoryWorkflow::class,
            workflowId: RepositoryWorkflow::getWorkflowId($repository),
            workflowIdReusePolicy: IdReusePolicy::AllowDuplicate,
            idConflictPolicy: WorkflowIdConflictPolicy::UseExisting,
        );
        $this->workflowClient->updateWithStart($stub, 'activate', startArgs: [$repository])->getResult();
    }

    public function touchRepository(GithubRepository $repository): void
    {
        $stub = WorkflowStub::workflow(
            $this->workflowClient->withTimeout(10),
            RepositoryWorkflow::class,
            workflowId: RepositoryWorkflow::getWorkflowId($repository),
            workflowIdReusePolicy: IdReusePolicy::AllowDuplicate,
            idConflictPolicy: WorkflowIdConflictPolicy::UseExisting,
        );
        $this->workflowClient->updateWithStart($stub, 'touch', startArgs: [$repository])->getResult();
    }

    public function deactivateRepository(GithubRepository $repository): void
    {
        $workflowId = RepositoryWorkflow::getWorkflowId($repository);

        try {
            $stub = $this->workflowClient
                ->withTimeout(10)
                ->newRunningWorkflowStub(RepositoryWorkflow::class, $workflowId);
            $stub->exit();
        } catch (WorkflowNotFoundException $e) {
            // Ignore if not found
        }
    }
}
