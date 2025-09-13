<?php

declare(strict_types=1);

namespace App\Module\Repository\Internal;

use App\Module\Github\Dto\GithubRepository;
use App\Module\Repository\Internal\Activity\RepositoryActivity;
use Spiral\TemporalBridge\Attribute\AssignWorker;
use Temporal\Exception\Failure\ApplicationFailure;
use Temporal\Support\Attribute\RetryPolicy;
use Temporal\Support\Attribute\TaskQueue;
use Temporal\Support\Factory\ActivityStub as A;
use Temporal\Support\Factory\WorkflowStub;
use Temporal\Workflow;
use Temporal\Workflow\CancellationScopeInterface;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

/**
 * Register repository in the system.
 * Create sync workflow for stars that will run periodically.
 */
#[WorkflowInterface]
#[AssignWorker('stargazer-github')]
#[TaskQueue('stargazer-github')]
#[RetryPolicy(attempts: 1)]
final class RepositoryWorkflow
{
    private bool $exit = false;
    private Workflow\Mutex $initLock;

    /** @var ScanRepositoryWorkflow $syncWorkflow */
    private object $syncWorkflow;

    #[Workflow\WorkflowInit]
    public function __construct(GithubRepository $repository)
    {
        # Check the Workflow ID format
        # It's required to guarantee uniqueness of the workflow per repository
        Workflow::getInfo()->execution->getID() === self::getWorkflowId($repository) or throw new ApplicationFailure(
            'Wrong Workflow ID format',
            'InvalidWorkflowId',
            nonRetryable: true,
        );

        $this->initLock = new Workflow\Mutex();
        $this->initLock->tryLock();
    }

    public static function getWorkflowId(GithubRepository $repository): string
    {
        return "repository_$repository";
    }

    #[WorkflowMethod]
    public function handle(GithubRepository $repository)
    {
        $info = yield A::activity(RepositoryActivity::class, retryAttempts: 1, startToCloseTimeout: 10)
            ->getGithubInfo($repository);

        # Persist repository with the info
        yield A::activity(RepositoryActivity::class, startToCloseTimeout: 10)->createOrUpdate($repository, $info);

        $scope = Workflow::async(function () use ($repository) {
            $this->syncWorkflow = WorkflowStub::childWorkflow(ScanRepositoryWorkflow::class);
            $this->initLock->unlock();

            yield $this->syncWorkflow->handle($repository, true);
        });

        yield Workflow::await(fn(): bool => $this->exit);
        $scope->cancel();
    }

    #[Workflow\UpdateMethod('activate')]
    public function activate()
    {
        yield $this->initLock;
        yield $this->syncWorkflow->resume();
    }

    #[Workflow\SignalMethod]
    public function pause()
    {
        yield $this->initLock;
        yield $this->syncWorkflow->pause();
    }

    #[Workflow\SignalMethod]
    public function exit(): void
    {
        $this->exit = true;
    }
}
