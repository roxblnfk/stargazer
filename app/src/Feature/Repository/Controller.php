<?php

declare(strict_types=1);

namespace App\Feature\Repository;

use App\Module\Github\Dto\GithubOwner;
use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\GithubService;
use App\Module\Repository\Exception\RepositoryAlreadyExists;
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
    public const ROUTE_ADD = 'repository:add';
    public const ROUTE_INFO = 'repository:info';

    public function __construct(
        private readonly ViewsInterface $views,
        private readonly RepositoryService $repositoryService,
    ) {}

    #[Route(route: '/repository/list', name: self::ROUTE_LIST, methods: ['GET'])]
    public function list(): mixed
    {
        return $this->views->render('repository:list', [
            'router' => $this->router,
        ]);
    }

    #[Route(route: '/repository/add', name: self::ROUTE_ADD, methods: ['POST'])]
    public function add(ServerRequestInterface $request): ResponseInterface
    {
        $repository = GithubRepository::fromString($request->getParsedBody()['repository_name'] ?? '');
        try {
            $this->repositoryService->registerRepository($repository);
        } catch (RepositoryAlreadyExists) {
            // Go to redirect
        }

        return $this->response->redirect($this->router->uri(self::ROUTE_INFO, [
            'owner' => (string) $repository->owner,
            'name' => $repository->name,
        ]));
    }

    #[Route(route: '/repository/info/<owner>/<name>', name: self::ROUTE_INFO, methods: ['GET'])]
    public function info(string $owner, string $name, GithubService $service): mixed
    {
        $repository = new GithubRepository(new GithubOwner($owner), $name);
        $repositoryInfo = $service->getRepositoryInfo($repository);

        return $this->views->render('repository:info', [
            'repository' => $repositoryInfo,
            'router' => $this->router,
        ]);
    }
}
