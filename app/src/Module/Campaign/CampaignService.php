<?php

declare(strict_types=1);

namespace App\Module\Campaign;

use App\Module\Campaign\DTO\Campaign;
use App\Module\Campaign\Internal\ORM\CampaignRepository;
use Spiral\Core\Attribute\Singleton;

#[Singleton]
class CampaignService
{
    public function __construct(
        private readonly CampaignRepository $campaignRepository,
    ) {}

    /**
     * @return \Iterator<int, Campaign>
     */
    public function getCampaigns(?bool $visibility = null): \Iterator
    {
        $q = $this->campaignRepository;
        $visibility === null or $q = $q->visible($visibility);
        foreach ($q->findAll() as $e) {
            yield $e->toDTO();
        }
    }
}
