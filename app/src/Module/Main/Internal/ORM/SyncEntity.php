<?php

declare(strict_types=1);

namespace App\Module\Main\Internal\ORM;

use App\Application\ORM\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\UpdatedAt;

#[Entity(
    role: 'stargazer_sync',
    table: 'stargazer_sync',
)]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
#[UpdatedAt(field: 'updatedAt', column: 'updated_at')]
class SyncEntity extends ActiveRecord
{
    #[Column(type: 'bigPrimary', name: 'id', typecast: 'int')]
    public int $id;

    /**
     * Repository Identifier on GitHub
     */
    #[Column(type: 'bigInteger', name: 'repo_id', typecast: 'int')]
    public int $repoId;

    #[Column(type: 'bigInteger', name: 'repo_stars', default: 0, typecast: 'int')]
    public int $repoStars = 0;

    #[Column(type: 'datetime', name: 'finished_at', nullable: true, typecast: 'datetime')]
    public ?\DateTimeInterface $finishedAt = null;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $updatedAt;

    #[Column(type: 'datetime', typecast: 'datetime')]
    public \DateTimeInterface $createdAt;

    public static function create(int $repoId): self
    {
        return self::make([
            'repoId' => $repoId,
        ]);
    }
}
