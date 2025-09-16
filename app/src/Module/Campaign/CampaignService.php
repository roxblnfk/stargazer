<?php

declare(strict_types=1);

namespace App\Module\Campaign;

use App\Module\Campaign\DTO\Campaign;
use App\Module\Campaign\Form\CreateCampaign;
use App\Module\Campaign\Form\UpdateCampaign;
use App\Module\Campaign\Internal\ORM\CampaignEntity;
use App\Module\Campaign\Internal\ORM\CampaignRepository;
use Ramsey\Uuid\UuidInterface;
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

    public function createCampaign(CreateCampaign $data): Campaign
    {
        $entity = CampaignEntity::create(
            title: $data->title,
            description: $data->description,
            startedAt: $data->startedAt,
            finishedAt: $data->finishedAt,
        );

        $entity->saveOrFail();
        return $entity->toDTO();
    }

    public function getCampaign(UuidInterface $uuid): Campaign
    {
        return $this->campaignRepository->findByPK($uuid)?->toDTO()
            ?? throw new \RuntimeException('Campaign not found.');
    }

    public function updateCampaign(UpdateCampaign $form): Campaign
    {
        $e = $this->campaignRepository->findByPK($form->uuid) ?? throw new \RuntimeException('Campaign not found.');
        $e->title = $form->title;
        $e->description = $form->description;
        $e->startedAt = $form->startedAt;
        $e->finishedAt = $form->finishedAt;
        $e->visible = $form->visible;
        $e->saveOrFail();
        return $e->toDTO();
    }

    public function deleteCampaign(UuidInterface $uuid): void
    {
        $e = $this->campaignRepository->findByPK($uuid) ?? throw new \RuntimeException('Campaign not found.');
        if ($e->countUsers > 0) {
            throw new \RuntimeException('Cannot delete a campaign with members.');
        }

        $e->deleteOrFail();
    }
}
