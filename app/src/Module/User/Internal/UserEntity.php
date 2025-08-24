<?php

declare(strict_types=1);

namespace App\Module\User\Internal;

use App\Module\Github\Dto\GithubOwner;
use App\Module\Github\Result\OwnerInfo;
use App\Module\ORM\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\UpdatedAt;

#[Entity(
    role: 'user',
    repository: RepoRepository::class,
    table: 'user',
)]
#[Index(columns: ['login'])]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
#[UpdatedAt(field: 'updatedAt', column: 'updated_at')]
class UserEntity extends ActiveRecord
{
    /**
     * Identifier on GitHub
     */
    #[Column(type: 'bigInteger', primary: true, typecast: 'int')]
    public int $id;

    #[Column(type: 'string')]
    public string $login;

    #[Column(type: 'json', nullable: true, typecast: [OwnerInfo::class, 'fromJsonString'])]
    public OwnerInfo $info;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $updatedAt;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $createdAt;

    public static function createFromOwnerInfo(OwnerInfo $info): self
    {
        return self::make([
            'id' => $info->id,
            'login' => $info->login,
            'info' => $info,
        ]);
    }

    public function toGithubOwner(): GithubOwner
    {
        return new GithubOwner(
            name: $this->login,
        );
    }
}
