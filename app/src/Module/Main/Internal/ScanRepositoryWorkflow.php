<?php

declare(strict_types=1);

namespace App\Module\Main\Internal;

use App\Module\Main\Internal\Activity\RepositoryActivity;
use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\Result\RepositoryInfo;
use React\Promise\PromiseInterface;
use Spiral\TemporalBridge\Attribute\AssignWorker;
use Temporal\Promise;
use Temporal\Support\Attribute\TaskQueue;
use Temporal\Support\Factory\ActivityStub as A;
use Temporal\Support\Factory\WorkflowStub;
use Temporal\Workflow;
use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
#[AssignWorker('stargazer-github')]
#[TaskQueue('stargazer-github')]
final class ScanRepositoryWorkflow
{
    private bool $now = true;

    /**
     * @var true
     */
    private bool $exit = false;

    private Workflow\Mutex $alive;

    #[Workflow\WorkflowInit]
    public function __construct(
        GithubRepository $repository,
        private bool $active = true,
    ) {
        $this->alive = new Workflow\Mutex();
        $this->alive->tryLock();
    }

    /**
     * @return PromiseInterface<null>
     */
    #[WorkflowMethod]
    public function handle(GithubRepository $repository, bool $active = true)
    {
        do {
            yield Workflow::awaitWithTimeout('3 hours', fn(): bool => $this->now, $this->alive);
            yield Workflow::await(fn(): bool => $this->active || $this->now, $this->alive);
            $this->now = false;

            # Exit is here
            if (!$this->alive->isLocked()) {
                return;
            }

            # Compose tasks
            $waits = [
                $this->updateCommonState($repository),
                Workflow::async(static function () use ($repository) {
                    yield WorkflowStub::childWorkflow(SyncStarsWorkflow::class)->handle($repository);
                }),
            ];

            # Wait for all tasks to complete or for exit signal
            yield Workflow::await(Promise::all($waits), $this->alive);
        } while (!Workflow::getInfo()->shouldContinueAsNew);

        # Continue as new
        yield Workflow::continueAsNew(Workflow::getInfo()->type->name, [$repository, $this->active]);
    }

    /**
     * @return PromiseInterface<null>
     */
    #[Workflow\SignalMethod]
    public function pause(): void
    {
        $this->active = false;
    }

    /**
     * @return PromiseInterface<null>
     */
    #[Workflow\SignalMethod]
    public function resume(): void
    {
        $this->active = true;
    }

    /**
     * @return PromiseInterface<null>
     */
    #[Workflow\SignalMethod]
    public function touch(): void
    {
        $this->now = true;
    }

    /**
     * @return PromiseInterface<null>
     */
    #[Workflow\SignalMethod]
    public function exit(): void
    {
        $this->alive->unlock();
    }

    private function updateCommonState(GithubRepository $repository): PromiseInterface
    {
        return A::activity(RepositoryActivity::class, retryAttempts: 1, startToCloseTimeout: 10)
            ->getGithubInfo($repository)
            ->then(
                # Persist repository with the info
                static fn(RepositoryInfo $info) => A::activity(RepositoryActivity::class, startToCloseTimeout: 10)
                    ->createOrUpdate($repository, $info),
            );
    }
}
