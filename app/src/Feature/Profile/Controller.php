<?php

declare(strict_types=1);

namespace App\Feature\Profile;

use App\Module\Data\RepositoryService;
use App\Module\Data\StargazerService;
use App\Module\Github\Exception\GitHubUserNotFoundException;
use Spiral\Prototype\Traits\PrototypeTrait;
use Spiral\Router\Annotation\Route;
use Spiral\Views\ViewsInterface;

final class Controller
{
    use PrototypeTrait;

    public const ROUTE_INDEX = 'profile:index';

    public function __construct(
        private readonly ViewsInterface $views,
        private readonly ProfileService $profileService,
        private readonly StargazerService $stargazerService,
        private readonly PointsService $pointsService,
        private readonly RepositoryService $repositoryService,
    ) {}

    #[Route(route: '/profile', name: self::ROUTE_INDEX, methods: ['GET'])]
    public function index(ProfileRequest $input): mixed
    {
        try {
            $user = $this->profileService->getInfo($input->username);
        } catch (GitHubUserNotFoundException) {
            return $this->response->redirect(
                $this->router->uri(\App\Feature\Index\Controller::ROUTE_INDEX),
            );
        }

        $stars = $this->stargazerService->getRepositoryIdsByUserId($user->id);

        return $this->views->render('profile:index', [
            'router' => $this->router,
            'user' => $user,
            'stars' => $stars,
            'points' => $this->pointsService->calculate($stars),
            'repositories' => $this->repositoryService->getTrackedRepositoriesInfo(),
        ]);
    }
}
