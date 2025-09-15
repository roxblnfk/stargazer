<?php

declare(strict_types=1);

namespace App\Module\Campaign\Internal\ORM;

use App\Application\ORM\ActiveRecord;
use App\Module\Campaign\DTO\CampaignUser;
use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\Dto\GithubUser;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\UpdatedAt;
use Ramsey\Uuid\UuidInterface;

#[Entity(
    role: 'campaign_user',
    table: 'campaign_user',
)]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
#[UpdatedAt(field: 'updatedAt', column: 'updated_at')]
class CampaignUserEntity extends ActiveRecord
{
    #[Column(type: 'uuid', name: 'campaign_uuid', primary: true, typecast: 'uuid')]
    public UuidInterface $campaignUuid;

    #[Column(type: 'bigInteger', name: 'user_id', primary: true, typecast: 'int')]
    public int $userId;

    #[Column(type: 'string', name: 'user_name', typecast: [GithubRepository::class, 'fromString'])]
    public GithubUser $userName;

    #[Column(type: 'bigInteger', default: 0, typecast: 'int')]
    public int $score = 0;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $updatedAt;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $createdAt;

    public static function create(
        int $userId,
        GithubUser $userName,
        UuidInterface $campaignId,
    ): self {
        return self::make([
            'campaignUuid' => $campaignId,
            'userId' => $userId,
            'userName' => (string) $userName,
        ]);
    }

    public function toDTO(): CampaignUser
    {
        return new CampaignUser(
            campaignUuid: $this->campaignUuid,
            userId: $this->userId,
            userName: $this->userName,
            score: $this->score,
            updatedAt: $this->updatedAt,
            createdAt: $this->createdAt,
        );
    }
}
