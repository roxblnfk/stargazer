<?php

declare(strict_types=1);

namespace App\Feature\Repository;

use App\Module\Github\Dto\GithubOrganization;
use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\GithubService;
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

    public function __construct(
        private readonly ViewsInterface $views,
    ) {}

    #[Route(route: '/repository/list', name: self::ROUTE_LIST, methods: ['GET'])]
    public function list(): mixed
    {
        return $this->views->render('repository:list', [
            'router' => $this->router,
        ]);
    }

    #[Route(route: '/repository/info', name: self::ROUTE_INFO, methods: ['GET'])]
    public function info(GithubService $service): mixed
    {
        return '';
        // return $this->views->render('repository:list', [
        //     'router' => $this->router,
        // ]);
    }
}
