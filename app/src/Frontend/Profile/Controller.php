<?php

declare(strict_types=1);

namespace App\Frontend\Profile;

use App\Frontend\Profile\Form\ProfileRequest;
use App\Module\Github\Dto\GithubUser;
use App\Module\Main\DTO\UnknownUser;
use App\Module\Main\RepositoryService;
use App\Module\Main\StargazerService;
use App\Module\Main\UserService;
use Spiral\Prototype\Traits\PrototypeTrait;
use Spiral\Router\Annotation\Route;
use Spiral\Views\ViewsInterface;

final class Controller
{
    use PrototypeTrait;

    public const ROUTE_INDEX = 'profile:index';

    public function __construct(
        private readonly ViewsInterface $views,
        private readonly StargazerService $stargazerService,
        private readonly PointsService $pointsService,
        private readonly RepositoryService $repositoryService,
        private readonly UserService $userService,
    ) {}

    #[Route(route: '/profile', name: self::ROUTE_INDEX, methods: ['GET'])]
    public function index(ProfileRequest $input): mixed
    {
        $username = new GithubUser($input->username);
        $user = $this->userService->getByUsername($username);

        if ($user instanceof UnknownUser) {
            return $this->views->render('profile:index-unknown', [
                'user' => $user,
                'repositories' => $this->repositoryService->getTrackedRepositoriesInfo(),
            ]);
        }

        $stars = $this->stargazerService->getRepositoryIdsByUserId($user->id);
        return $this->views->render('profile:index-known', [
            'user' => $user,
            'stars' => $stars,
            'points' => $this->pointsService->calculate($stars),
            'repositories' => $this->repositoryService->getTrackedRepositoriesInfo(),
        ]);
    }
}
