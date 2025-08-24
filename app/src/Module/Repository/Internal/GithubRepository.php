<?php

declare(strict_types=1);

namespace App\Module\Repository\Internal;

use App\Module\ORM\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\Uuid\Uuid7;
use Ramsey\Uuid\UuidInterface;

#[Entity(
    table: 'github_token',
)]
#[Uuid7(field: 'uuid', column: 'uuid')]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
class GithubRepository extends ActiveRecord
{
    public ?UuidInterface $uuid = null;

    #[Column(type: 'string')]
    public string $name;

    public \DateTimeInterface $createdAt;

    public static function create(string $name): self
    {
        return self::make([
            'name' => $name,
        ]);
    }
}
