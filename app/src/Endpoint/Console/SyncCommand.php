<?php

declare(strict_types=1);

namespace App\Endpoint\Console;

use App\Module\Github\Dto\GithubRepository;
use App\Module\Stargazer\SyncService;
use Spiral\Console\Attribute\Argument;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Attribute\Question;
use Spiral\Console\Command;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
#[AsCommand(name: 'sync:stars', description: 'Synchronize github stars for the given site.')]
final class SyncCommand extends Command
{
    /**
     * @var non-empty-string
     */
    #[Argument(description: 'Repository name')]
    #[Question(question: 'Provide a repository name (e.g. user/repo)')]
    private string $repository;

    public function __invoke(SyncService $syncService): int
    {
        $syncService->syncStars(GithubRepository::fromString($this->repository));

        return self::SUCCESS;
    }
}
