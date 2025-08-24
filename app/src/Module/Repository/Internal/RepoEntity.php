<?php

declare(strict_types=1);

namespace App\Module\Repository\Internal;

use App\Module\Github\Dto\GithubOwner;
use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\Result\RepositoryInfo;
use App\Module\ORM\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;

#[Entity(
    role: 'repo',
    repository: RepoRepository::class,
    table: 'repository',
)]
#[Index(columns: ['name'])]
class RepoEntity extends ActiveRecord
{
    /**
     * Identifier on GitHub
     */
    #[Column(type: 'bigInteger', primary: true, typecast: 'int')]
    public int $id;

    #[Column(type: 'string')]
    public string $owner;

    #[Column(type: 'string')]
    public string $name;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $createdAt;

    #[Column(type: 'boolean', default: true, typecast: 'bool')]
    public bool $active = true;

    public static function createFromRepositoryInfo(RepositoryInfo $info): self
    {
        return self::make([
            'id' => $info->id,
            'owner' => $info->owner->login,
            'name' => $info->name,
            'createdAt' => $info->createdAt,
        ]);
    }

    public function toGithubRepository(): GithubRepository
    {
        return new GithubRepository(
            owner: new GithubOwner($this->owner),
            name: $this->name,
        );
    }
}
