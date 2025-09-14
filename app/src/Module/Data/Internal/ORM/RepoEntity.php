<?php

declare(strict_types=1);

namespace App\Module\Data\Internal\ORM;

use App\Application\ORM\ActiveRecord;
use App\Module\Data\DTO\Repository;
use App\Module\Github\Dto\GithubOwner;
use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\Result\RepositoryInfo;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\UpdatedAt;

#[Entity(
    role: 'repo',
    repository: RepoRepository::class,
    table: 'repository',
)]
#[Index(columns: ['name'])]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
#[UpdatedAt(field: 'updatedAt', column: 'updated_at')]
class RepoEntity extends ActiveRecord
{
    /**
     * Identifier on GitHub
     */
    #[Column(type: 'bigInteger', primary: true, typecast: 'int')]
    public int $id;

    #[Column(type: 'string')]
    public string $owner;

    #[Column(type: 'bigInteger', typecast: 'int')]
    public int $ownerId;

    #[Column(type: 'string')]
    public string $name;

    #[Column(type: 'boolean', default: true, typecast: 'bool')]
    public bool $active = false;

    #[Column(type: 'json', nullable: true, typecast: [RepositoryInfo::class, 'fromJsonString'])]
    public RepositoryInfo $info;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $updatedAt;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $createdAt;

    public static function createFromRepositoryInfo(RepositoryInfo $info): self
    {
        return self::make([
            'id' => $info->id,
            'owner' => $info->owner->login,
            'ownerId' => $info->owner->id,
            'name' => $info->name,
            'info' => $info,
        ]);
    }

    public function toGithubRepository(): GithubRepository
    {
        return new GithubRepository(
            owner: new GithubOwner($this->owner),
            name: $this->name,
        );
    }

    public function toDTO(): Repository
    {
        return new Repository(
            id: $this->id,
            owner: $this->owner,
            ownerId: $this->ownerId,
            name: $this->name,
            active: $this->active,
            info: $this->info ?? null,
            updatedAt: $this->updatedAt,
            createdAt: $this->createdAt,
        );
    }
}
