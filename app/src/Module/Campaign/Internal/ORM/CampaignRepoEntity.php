<?php

declare(strict_types=1);

namespace App\Module\Campaign\Internal\ORM;

use App\Application\ORM\ActiveRecord;
use App\Module\Campaign\DTO\CampaignRepo;
use App\Module\Github\Dto\GithubRepository;
use App\Module\Main\Internal\ORM\RepoEntity;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\UpdatedAt;
use Ramsey\Uuid\UuidInterface;

/**
 * This entity represents a repository included in a campaign.
 *
 * It holds information about the repository's ID, name, associated campaign,
 * and a score that might represent its importance or relevance within the campaign.
 */
#[Entity(
    role: 'campaign_repo',
    repository: CampaignRepoRepository::class,
    table: 'campaign_repo',
)]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
#[UpdatedAt(field: 'updatedAt', column: 'updated_at')]
class CampaignRepoEntity extends ActiveRecord
{
    #[Column(type: 'uuid', name: 'campaign_uuid', primary: true, typecast: 'uuid')]
    public UuidInterface $campaignUuid;

    #[Column(type: 'bigInteger', name: 'repo_id', primary: true, typecast: 'int')]
    public int $repoId;

    #[Column(type: 'string', name: 'repo_name', typecast: [GithubRepository::class, 'fromString'])]
    public GithubRepository $repoName;

    #[Column(type: 'bigInteger', default: 1, typecast: 'int')]
    public int $score = 1;

    /** Stars harvested during the campaign from participants only */
    #[Column(type: 'bigInteger', name: 'count_stars', default: 0, typecast: 'int')]
    public int $countStars = 0;

    /** All the Stars harvested during the campaign */
    #[Column(type: 'bigInteger', name: 'count_stars_at_all', default: 0, typecast: 'int')]
    public int $countStarsAtAll = 0;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $updatedAt;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $createdAt;

    #[BelongsTo(target: RepoEntity::class, innerKey: 'repoId', outerKey: 'id', cascade: false, nullable: true)]
    public ?RepoEntity $repository;

    public static function create(
        int $repoId,
        GithubRepository $repoName,
        UuidInterface $campaignId,
        int $score = 1,
    ): self {
        return self::make([
            'campaignUuid' => $campaignId,
            'repoId' => $repoId,
            'repoName' => $repoName,
            'score' => $score,
        ]);
    }

    public function toDTO(): CampaignRepo
    {
        return new CampaignRepo(
            campaignUuid: $this->campaignUuid,
            repoId: $this->repoId,
            repoName: $this->repoName,
            score: $this->score,
            countStars: $this->countStars,
            countStarsAtAll: $this->countStarsAtAll,
            updatedAt: $this->updatedAt,
            createdAt: $this->createdAt,
        );
    }
}
