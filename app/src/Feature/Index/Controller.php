<?php

declare(strict_types=1);

namespace App\Feature\Index;

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
    public function index(): mixed
    {
        return $this->views->render('index:home', [
            'router' => $this->router,
        ]);
    }
}
