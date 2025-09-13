<?php

declare(strict_types=1);

namespace App\Feature\Repository;

use App\Module\Github\Dto\GithubOwner;
use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\GithubService;
use App\Module\Repository\RepositoryService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\Prototype\Traits\PrototypeTrait;
use Spiral\Router\Annotation\Route;
use Spiral\Views\ViewsInterface;

/**
 * Simple home page controller. It renders home page template.
 */
final class Controller
{
    use PrototypeTrait;

    public const ROUTE_LIST = 'repository:list';
    public const ROUTE_INFO = 'repository:info';
    public const ROUTE_ACTIVATE = 'repository:activate';
    public const ROUTE_DEACTIVATE = 'repository:deactivate';
    public const ROUTE_TOUCH = 'repository:touch';

    public function __construct(
        private readonly ViewsInterface $views,
        private readonly RepositoryService $repositoryService,
    ) {}

    #[Route(route: '/repository/list', name: self::ROUTE_LIST, methods: ['GET'])]
    public function list(): mixed
    {
        return $this->views->render('repository:list', [
            'router' => $this->router,
            'repositories' => $this->repositoryService->getRepositories(),
        ]);
    }

    #[Route(route: '/repository/info/<owner>/<name>', name: self::ROUTE_INFO, methods: ['GET'])]
    public function info(string $owner, string $name): mixed
    {
        $repository = new GithubRepository(new GithubOwner($owner), $name);
        $repositoryInfo = $this->repositoryService->getRepository($repository);

        return $this->views->render('repository:info', [
            'repository' => $repositoryInfo,
            'router' => $this->router,
        ]);
    }

    #[Route(route: '/repository/touch', name: self::ROUTE_TOUCH, methods: ['POST'])]
    public function touch(ServerRequestInterface $request): void
    {
        $repository = GithubRepository::fromString($request->getParsedBody()['repository_name'] ?? '');
        $this->repositoryService->touchRepository($repository);
    }

    #[Route(route: '/repository/activate', name: self::ROUTE_ACTIVATE, methods: ['POST'])]
    public function activate(ServerRequestInterface $request): void
    {
        $repository = GithubRepository::fromString($request->getParsedBody()['repository_name'] ?? '');
        $this->repositoryService->activateRepository($repository);
    }

    #[Route(route: '/repository/deactivate', name: self::ROUTE_DEACTIVATE, methods: ['POST'])]
    public function deactivate(ServerRequestInterface $request): void
    {
        $repository = GithubRepository::fromString($request->getParsedBody()['repository_name'] ?? '');
        $this->repositoryService->deactivateRepository($repository);
    }
}
