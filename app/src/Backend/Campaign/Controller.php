<?php

declare(strict_types=1);

namespace App\Backend\Campaign;

use App\Module\Campaign\CampaignService;
use App\Module\Campaign\Form\CreateCampaign;
use App\Module\Campaign\Form\UpdateCampaign;
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
