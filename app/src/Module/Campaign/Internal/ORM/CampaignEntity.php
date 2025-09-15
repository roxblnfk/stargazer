<?php

declare(strict_types=1);

namespace App\Module\Campaign\Internal\ORM;

use App\Application\ORM\ActiveRecord;
use App\Module\Campaign\DTO\Campaign;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\UpdatedAt;
use Cycle\ORM\Entity\Behavior\Uuid\Uuid7;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[Entity(
    role: 'campaign',
    repository: CampaignRepository::class,
    table: 'campaign',
)]
#[Index(['finished_at'])]
#[Uuid7]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
#[UpdatedAt(field: 'updatedAt', column: 'updated_at')]
class CampaignEntity extends ActiveRecord
{
    #[Column(type: 'uuid', primary: true, typecast: 'uuid')]
    public UuidInterface $uuid;

    /** @var non-empty-string */
    #[Column(type: 'string')]
    public string $title;

    /** @var non-empty-string */
    #[Column(type: 'text')]
    public string $description;

    #[Column(type: 'boolean', default: true)]
    public bool $visible = true;

    /**
     * Start time of active campaign. This time stars will be harvested actively.
     */
    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $startedAt;

    #[Column(type: 'datetime', name: 'finished_at', nullable: true, typecast: 'datetime')]
    public ?\DateTimeInterface $finishedAt;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $updatedAt;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $createdAt;

    #[HasMany(target: CampaignRepoEntity::class, innerKey: 'uuid', outerKey: 'campaignUuid')]
    public array $repositories = [];

    #[HasMany(target: CampaignUserEntity::class, innerKey: 'uuid', outerKey: 'campaignUuid')]
    public array $members = [];

    public static function create(\DateTimeInterface $startedAt): self
    {
        $uuid = Uuid::uuid7();
        return self::make([
            'uuid' => $uuid,
            'startedAt' => $startedAt,
        ]);
    }

    public function toDTO(): Campaign
    {
        return new Campaign(
            uuid: $this->uuid,
            title: $this->title,
            description: $this->description,
            visible: $this->visible,
            startedAt: $this->startedAt,
            finishedAt: $this->finishedAt,
            repositoryCount: \count($this->repositories),
            memberCount: \count($this->members),
            updatedAt: $this->updatedAt,
            createdAt: $this->createdAt,
        );
    }
}
