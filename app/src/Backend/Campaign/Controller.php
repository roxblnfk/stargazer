<?php

declare(strict_types=1);

namespace App\Backend\Campaign;

use App\Module\Campaign\CampaignService;
use App\Module\Campaign\Form\CreateCampaign;
use App\Module\Campaign\Form\UpdateCampaign;
use App\Module\Github\Dto\GithubOwner;
use App\Module\Github\Dto\GithubRepository;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Spiral\Http\ResponseWrapper;
use Spiral\Router\Annotation\Route;
use Spiral\Router\RouterInterface;
use Spiral\Views\ViewsInterface;

final class Controller
{
    public const ROUTE_LIST = 'campaign:list';
    public const ROUTE_INFO = 'campaign:info';
    public const ROUTE_MEMBERS = 'campaign:members';
    public const ROUTE_REPOS = 'campaign:repos';
    public const ROUTE_REPOS_ADDED = 'campaign:repos-added';
    public const ROUTE_REPOS_AVAILABLE = 'campaign:repos-available';
    public const ROUTE_REPO_ADD = 'campaign:repo-add';
    public const ROUTE_REPO_REMOVE = 'campaign:repo-remove';
    public const ROUTE_CREATE = 'campaign:create';
    public const ROUTE_EDIT = 'campaign:edit';
    public const ROUTE_STORE = 'campaign:store';
    public const ROUTE_UPDATE = 'campaign:update';
    public const ROUTE_DELETE = 'campaign:delete';
    public const ROUTE_TOGGLE_VISIBILITY = 'campaign:toggle-visibility';

    public function __construct(
        private readonly ViewsInterface $views,
        private readonly RouterInterface $router,
        private readonly CampaignService $campaignService,
        private readonly ResponseWrapper $response,
    ) {}

    #[Route(route: '/campaign/list', name: self::ROUTE_LIST, methods: ['GET'], group: 'backend')]
    public function list(): mixed
    {
        return $this->views->render('campaign:list', [
            'campaigns' => $this->campaignService->getCampaigns(),
        ]);
    }

    #[Route(route: '/campaign/info/<uuid>', name: self::ROUTE_INFO, methods: ['GET'], group: 'backend')]
    public function info(string $uuid): mixed
    {
        $uuid = Uuid::fromString($uuid);
        $campaign = $this->campaignService->getCampaign($uuid);

        return $this->views->render('campaign:info', [
            'campaign' => $campaign,
        ]);
    }

    #[Route(route: '/campaign/members/<uuid>', name: self::ROUTE_MEMBERS, methods: ['GET'], group: 'backend')]
    public function members(string $uuid): string
    {
        $uuid = Uuid::fromString($uuid);
        $campaign = $this->campaignService->getCampaign($uuid);

        // TODO: Replace with actual service call
        $members = $this->campaignService->getCampaignMembers($uuid);

        return $this->views->render('campaign:members', [
            'campaign' => $campaign,
            'members' => $members,
        ]);
    }

    #[Route(route: '/campaign/repos/<uuid>', name: self::ROUTE_REPOS, methods: ['GET'], group: 'backend')]
    public function repos(string $uuid): string
    {
        $uuid = Uuid::fromString($uuid);
        $campaign = $this->campaignService->getCampaign($uuid);

        return $this->views->render('campaign:repos', [
            'campaign' => $campaign,
        ]);
    }

    #[Route(route: '/campaign/repos-added/<uuid>', name: self::ROUTE_REPOS_ADDED, methods: ['GET'], group: 'backend')]
    public function reposAdded(string $uuid): string
    {
        $uuid = Uuid::fromString($uuid);
        $campaign = $this->campaignService->getCampaign($uuid);

        $addedRepos = $this->campaignService->getAddedRepos($uuid);

        return $this->views->render('campaign:repos-added', [
            'campaign' => $campaign,
            'addedRepos' => $addedRepos,
        ]);
    }

    #[Route(route: '/campaign/repos-available/<uuid>', name: self::ROUTE_REPOS_AVAILABLE, methods: ['GET'], group: 'backend')]
    public function reposAvailable(string $uuid): string
    {
        $uuid = Uuid::fromString($uuid);
        $campaign = $this->campaignService->getCampaign($uuid);

        $availableRepos = \iterator_to_array($this->campaignService->getNotAddedRepos($uuid));

        return $this->views->render('campaign:repos-available', [
            'campaign' => $campaign,
            'availableRepos' => $availableRepos,
        ]);
    }

    #[Route(route: '/campaign/repo-add/<uuid>/<owner>/<name>', name: self::ROUTE_REPO_ADD, methods: ['POST'], group: 'backend')]
    public function repoAdd(string $uuid, string $owner, string $name): void
    {
        $uuid = Uuid::fromString($uuid);
        $repository = new GithubRepository(new GithubOwner($owner), $name);

        $this->campaignService->addRepoToCampaign($uuid, $repository);
    }

    #[Route(route: '/campaign/repo-remove/<uuid>/<owner>/<name>', name: self::ROUTE_REPO_REMOVE, methods: ['DELETE'], group: 'backend')]
    public function repoRemove(string $uuid, string $owner, string $name): void
    {
        $uuid = Uuid::fromString($uuid);
        $repository = new GithubRepository(new GithubOwner($owner), $name);

        $this->campaignService->removeRepoFromCampaign($uuid, $repository);
    }

    #[Route(route: '/campaign/create', name: self::ROUTE_CREATE, methods: ['GET'], group: 'backend')]
    public function create(): mixed
    {
        return $this->views->render('campaign:form', [
            'campaign' => null,
        ]);
    }

    #[Route(route: '/campaign/store', name: self::ROUTE_STORE, methods: ['POST'], group: 'backend')]
    public function store(CreateCampaign $form): ResponseInterface
    {
        $campaign = $this->campaignService->createCampaign($form);

        return $this->response->redirect($this->router->uri(self::ROUTE_INFO, ['uuid' => $campaign->uuid]));
    }

    #[Route(route: '/campaign/edit/<uuid>', name: self::ROUTE_EDIT, methods: ['GET'], group: 'backend')]
    public function edit(string $uuid): mixed
    {
        $uuid = Uuid::fromString($uuid);
        $campaign = $this->campaignService->getCampaign($uuid);

        return $this->views->render('campaign:form', [
            'campaign' => $campaign,
        ]);
    }

    #[Route(route: '/campaign/update', name: self::ROUTE_UPDATE, methods: ['POST'], group: 'backend')]
    public function update(UpdateCampaign $form): mixed
    {
        $campaign = $this->campaignService->updateCampaign($form);

        return $this->response->redirect($this->router->uri(self::ROUTE_INFO, ['uuid' => $campaign->uuid]));
    }

    #[Route(route: '/campaign/delete/<uuid>', name: self::ROUTE_DELETE, methods: ['POST'], group: 'backend')]
    public function delete(string $uuid): ResponseInterface
    {
        $uuid = Uuid::fromString($uuid);
        $this->campaignService->deleteCampaign($uuid);

        return $this->response->redirect($this->router->uri(self::ROUTE_LIST));
    }

    #[Route(route: '/campaign/toggle-visibility/<uuid>', name: self::ROUTE_TOGGLE_VISIBILITY, methods: ['POST'], group: 'backend')]
    public function toggleVisibility(string $uuid): ResponseInterface
    {
        $uuid = Uuid::fromString($uuid);
        $visible = $this->campaignService->toggleVisibility($uuid);

        return $this->response->redirect($this->router->uri(self::ROUTE_INFO, ['uuid' => $uuid]));
    }
}
