<?php

declare(strict_types=1);

namespace App\Backend\Campaign;

use App\Module\Campaign\CampaignService;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\Router\Annotation\Route;
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
        private readonly CampaignService $campaignService,
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
    public function store(ServerRequestInterface $request): mixed
    {
        $data = $request->getParsedBody();
        $campaign = $this->campaignService->createCampaign($data);

        return $this->views->render('campaign:info', [
            'campaign' => $campaign,
        ]);
    }

    #[Route(route: '/campaign/edit/<uuid>', name: self::ROUTE_EDIT, methods: ['GET'], group: 'backend')]
    public function edit(string $uuid): mixed
    {
        $campaign = $this->campaignService->getCampaign($uuid);

        return $this->views->render('campaign:form', [
            'campaign' => $campaign,
        ]);
    }

    #[Route(route: '/campaign/update/<uuid>', name: self::ROUTE_UPDATE, methods: ['POST'], group: 'backend')]
    public function update(string $uuid, ServerRequestInterface $request): mixed
    {
        $data = $request->getParsedBody();
        $campaign = $this->campaignService->updateCampaign($uuid, $data);

        return $this->views->render('campaign:info', [
            'campaign' => $campaign,
        ]);
    }

    #[Route(route: '/campaign/delete/<uuid>', name: self::ROUTE_DELETE, methods: ['POST'], group: 'backend')]
    public function delete(string $uuid): array
    {
        $this->campaignService->deleteCampaign($uuid);

        return ['deleted' => true];
    }

    #[Route(route: '/campaign/toggle-visibility/<uuid>', name: self::ROUTE_TOGGLE_VISIBILITY, methods: ['POST'], group: 'backend')]
    public function toggleVisibility(string $uuid): array
    {
        $visible = $this->campaignService->toggleVisibility($uuid);

        return ['visible' => $visible];
    }
}