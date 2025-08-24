<?php

declare(strict_types=1);

namespace App\Feature\Index;

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

    public const ROUTE_INDEX = 'index1';

    public function __construct(
        private readonly ViewsInterface $views,
    ) {}

    #[Route(route: '/', name: self::ROUTE_INDEX, methods: ['GET'])]
    public function index(GithubService $service): mixed
    {
        return $this->views->render('index:home', [
            'router' => $this->router,
        ]);
    }
}
