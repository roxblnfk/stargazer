<?php

declare(strict_types=1);

namespace App\Module\Github\Internal;

use App\Module\ORM\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\Uuid\Uuid7;
use Ramsey\Uuid\UuidInterface;

#[Entity(
    role: 'github-token',
    table: 'github_token',
)]
#[Uuid7(field: 'uuid', column: 'uuid')]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
class GithubToken extends ActiveRecord implements \Stringable
{
    public ?UuidInterface $uuid = null;

    #[Column(type: 'string')]
    public string $value;

    public \DateTimeInterface $createdAt;

    public function __toString(): string
    {
        return $this->value;
    }
}
