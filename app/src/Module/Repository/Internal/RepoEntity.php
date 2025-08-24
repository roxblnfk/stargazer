<?php

declare(strict_types=1);

namespace App\Module\Repository\Internal;

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

    public static function createFromRepositoryInfo(RepositoryInfo $info): self
    {
        return self::make([
            'id' => $info->id,
            'owner' => $info->owner->login,
            'name' => $info->name,
            'createdAt' => $info->createdAt,
        ]);
    }
}
