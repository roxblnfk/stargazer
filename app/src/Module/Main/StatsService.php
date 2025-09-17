<?php

declare(strict_types=1);

namespace App\Module\Main;

use App\Module\Campaign\CampaignService;
use App\Module\Github\Internal\GithubTokenEntity;
use App\Module\Main\DTO\Dashboard;
use App\Module\Main\Internal\ORM\RepoEntity;
use App\Module\Main\Internal\ORM\StarEntity;
use App\Module\Main\Internal\ORM\UserEntity;
use Spiral\Core\Attribute\Singleton;
use Spiral\Prototype\Traits\PrototypeTrait;

#[Singleton]
class StatsService
{
    use PrototypeTrait;

    public function __construct(
        private readonly CampaignService $campaignService,
    ) {}

    public function dashboard(): Dashboard
    {
        return new Dashboard(
            countCampaigns: 0,
            countRepositories: RepoEntity::query()->count(),
            countUsers: UserEntity::query()->count(),
            countStars: StarEntity::query()->count(),
            countTokens: GithubTokenEntity::query()->count(),
        );
    }
}
