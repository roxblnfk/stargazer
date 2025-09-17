<?php

declare(strict_types=1);

namespace App\Module\Github\Internal;

use App\Application\ORM\ActiveRecord;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Entity\Behavior\CreatedAt;
use Cycle\ORM\Entity\Behavior\Uuid\Uuid7;
use Ramsey\Uuid\UuidInterface;

#[Entity(
    role: 'github_token',
    table: 'github_token',
)]
#[Index(['used_at'])]
#[Uuid7(field: 'uuid', column: 'uuid')]
#[CreatedAt(field: 'createdAt', column: 'created_at')]
class GithubTokenEntity extends ActiveRecord implements \Stringable
{
    #[Column(type: 'uuid', primary: true, typecast: 'uuid')]
    public ?UuidInterface $uuid = null;

    #[Column(type: 'string')]
    public string $value;

    #[Column(type: 'datetime', name: 'expires_at', nullable: true, typecast: 'datetime')]
    public ?\DateTimeImmutable $expiresAt = null;

    #[Column(type: 'datetime', name: 'used_at', nullable: false, typecast: 'datetime')]
    public \DateTimeImmutable $usedAt;

    public \DateTimeInterface $createdAt;

    public static function create(string $value, ?\DateTimeImmutable $expiresAt): static
    {
        return static::make([
            'value' => $value,
            'expiresAt' => $expiresAt,
            'usedAt' => new \DateTimeImmutable(),
        ]);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
