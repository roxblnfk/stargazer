<?php

declare(strict_types=1);

namespace App\Module\Stargazer\Internal;

use App\Module\ORM\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\UpdatedAt;

#[Entity(
    role: 'star',
    repository: StarRepository::class,
    table: 'stargazer',
)]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
#[UpdatedAt(field: 'updatedAt', column: 'updated_at')]
class StarEntity extends ActiveRecord
{
    /**
     * User Identifier on GitHub
     */
    #[Column(type: 'bigInteger', name: 'user_id', primary: true, typecast: 'int')]
    public int $userId;

    /**
     * Repository Identifier on GitHub
     */
    #[Column(type: 'bigInteger', name: 'repo_id', primary: true, typecast: 'int')]
    public int $repoId;

    #[Column(type: 'datetime', name: 'starred_at', typecast: 'datetime')]
    public ?\DateTimeInterface $starredAt = null;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $updatedAt;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $createdAt;

    #[Column(type: 'integer', name: 'last_sync_id')]
    public int $lastSyncId;

    public static function create(int $userId, int $repoId, int $syncId, \DateTimeInterface $starredAt): self
    {
        return self::make([
            'userId' => $userId,
            'repoId' => $repoId,
            'starredAt' => $starredAt,
            'lastSyncId' => $syncId,
        ]);
    }
}
