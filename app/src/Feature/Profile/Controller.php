<?php

declare(strict_types=1);

namespace App\Feature\Profile;

use App\Module\Github\GithubService;
use App\Module\Repository\RepositoryService;
use App\Module\Stargazer\StargazerService;
use App\Module\User\UserService;
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
        private readonly UserService $userService,
        private readonly GithubService $githubService,
        private readonly StargazerService $stargazerService,
    ) {}

    #[Route(route: '/profile', name: self::ROUTE_INDEX, methods: ['GET'])]
    public function index(
        ProfileRequest $input,
    ): mixed {
        $user = $this->userService->getByUsername($input->username);

        if ($user === null) {
            try {
                $info = $this->githubService->getUserInfo($input->username);
            } catch (\Throwable) {
                return $this->response->redirect(
                    $this->router->uri(\App\Feature\Index\Controller::ROUTE_INDEX),
                );
            }

            $user = $this->userService->getOrCreate($info);
        }

        $stars = $this->stargazerService->getRepositoryIdsByUserId($user->id);
        $points = count($stars);

        return $this->views->render('profile:index', [
            'router' => $this->router,
            'user' => $user->info,
            'stars' => $stars,
            'points' => $points,
            'repositories' => $this->repositoryService->getTrackedRepositoriesInfo(),
        ]);
    }
}
