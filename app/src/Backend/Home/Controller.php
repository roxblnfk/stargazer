<?php

declare(strict_types=1);

namespace App\Backend\Home;

use App\Backend\Home\Form\AuthTokenRequest;
use App\Backend\Home\Form\GithubTokenRequest;
use App\Module\Github\GithubService;
use App\Module\Main\StatsService;
use Psr\Http\Message\ResponseInterface;
use Spiral\Auth\AuthContextInterface;
use Spiral\Auth\TokenStorageInterface;
use Spiral\Http\ResponseWrapper;
use Spiral\Router\Annotation\Route;
use Spiral\Router\RouterInterface;
use Spiral\Views\ViewsInterface;

/**
 * Simple home page controller. It renders home page template.
 */
final class Controller
{
    public const ROUTE_AUTH = 'home:auth';
    public const ROUTE_INDEX = 'home:index';
    public const ROUTE_ADD_TOKEN = 'home:add-github-token';

    public function __construct(
        private readonly ResponseWrapper $response,
        private readonly ViewsInterface $views,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RouterInterface $router,
    ) {}

    #[Route(route: '/auth', name: self::ROUTE_AUTH, methods: ['GET'], group: 'backend')]
    public function auth(AuthContextInterface $auth, AuthTokenRequest $form): ResponseInterface
    {
        $token = $this->tokenStorage->load($form->token);
        if ($token === null) {
            return $this->response->html('nope');
        }

        $auth->start($token);
        return $this->response->redirect($this->router->uri(self::ROUTE_INDEX));
    }

    #[Route(route: '/index', name: self::ROUTE_INDEX, methods: ['GET'], group: 'backend')]
    public function index(StatsService $stats): mixed
    {
        return $this->views->render('backend-home:index', [
            'router' => $this->router,
            'dashboard' => $stats->dashboard(),
        ]);
    }

    #[Route(route: '/add-github-token', name: self::ROUTE_ADD_TOKEN, methods: ['POST'], group: 'backend')]
    public function addToken(GithubTokenRequest $form, GithubService $github): void
    {
        $github->addToken(\trim($form->token), $form->expiresAt);
    }
}
