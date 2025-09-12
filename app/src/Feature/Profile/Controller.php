<?php

declare(strict_types=1);

namespace App\Feature\Profile;

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

    public const ROUTE_INDEX = 'profile:index';

    public function __construct(
        private readonly ViewsInterface $views,
        private readonly RepositoryService $repositoryService,
    ) {}

    #[Route(route: '/profile', name: self::ROUTE_INDEX, methods: ['GET'])]
    public function index(): mixed
    {
        return $this->views->render('profile:index', [
            'router' => $this->router,
            'repositories' => $this->repositoryService->getTrackedRepositoriesInfo(),
        ]);
    }
}
