<?php

declare(strict_types=1);

namespace App\Module\Main;

use App\Application\ORM\ActiveRecord;
use App\Module\Main\DTO\Repository;
use App\Module\Main\Internal\ORM\RepoRepository;
use App\Module\Main\Internal\RepositoryWorkflow;
use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\Result\RepositoryInfo;
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
     * @param array<int, int> $exclude List of repository IDs to exclude
     *
     * @return \Iterator<int, Repository>
     */
    public function getRepositories(?bool $active = null, array $exclude = []): \Iterator
    {
        $q = $this->repoRepository;
        $active === null or $q = $q->active($active);
        $exclude === [] or $q = $q->exclude($exclude);
        foreach ($q->findAll() as $repo) {
            yield $repo->toDTO();
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

    public function getRepository(GithubRepository $repository): Repository
    {
        return $this->repoRepository
            ->whereFullName($repository)
            ->findOne()?->toDTO() ?? throw new \RuntimeException('Repository for found.');
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

    public function setVisible(GithubRepository $repository, bool $value = true): void
    {
        ActiveRecord::transact(function () use ($repository, $value): void {
            $repo = $this->repoRepository->forUpdate()->whereFullName($repository)->findOne();
            $repo->active = $value;
            $repo->saveOrFail();
        });
    }
}
