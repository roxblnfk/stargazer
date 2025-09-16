<?php

declare(strict_types=1);

namespace App\Frontend\Profile;

use App\Frontend\Profile\Form\ProfileRequest;
use App\Module\Github\Dto\GithubUser;
use App\Module\Main\DTO\UnknownUser;
use App\Module\Main\DTO\User;
use App\Module\Main\RepositoryService;
use App\Module\Main\StargazerService;
use App\Module\Main\UserService;
use Spiral\Prototype\Traits\PrototypeTrait;
use Spiral\Router\Annotation\Route;
use Spiral\Session\SessionInterface;
use Spiral\Views\ViewsInterface;

final class Controller
{
    use PrototypeTrait;

    public const ROUTE_ENTER = 'profile:enter';
    public const ROUTE_INDEX = 'profile:index';

    public function __construct(
        private readonly ViewsInterface $views,
        private readonly StargazerService $stargazerService,
        private readonly PointsService $pointsService,
        private readonly RepositoryService $repositoryService,
        private readonly UserService $userService,
    ) {}

    #[Route(route: '/enter', name: self::ROUTE_ENTER, methods: ['GET'])]
    public function enter(ProfileRequest $input, SessionInterface $session): mixed
    {
        $username = new GithubUser($input->username);
        $session->getSection('user')->set('name', $username->name);

        if (false) {
            // todo: if a campaign code was provided

            // todo: find the campaign by invite code

            $user = $this->userService->getByUsername($username);
            if ($user instanceof User) {
                // todo: signup to a campaign if campaign code provided
            }
        }

        return $this->response->redirect($this->router->uri(self::ROUTE_INDEX, [
            'name' => $username->name,
        ]));
    }

    #[Route(route: '/profile/<name>', name: self::ROUTE_INDEX, methods: ['GET'])]
    public function index(string $name): mixed
    {
        $username = new GithubUser($name);
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
