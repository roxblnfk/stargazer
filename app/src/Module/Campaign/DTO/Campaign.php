<?php

declare(strict_types=1);

namespace App\Module\Campaign\DTO;

use Ramsey\Uuid\UuidInterface;

final class Campaign implements \Stringable
{
    public function __construct(
        public readonly UuidInterface $uuid,
        public readonly string $title,
        public readonly string $description,
        public readonly bool $visible,
        public readonly \DateTimeInterface $startedAt,
        public readonly ?\DateTimeInterface $finishedAt,
        public readonly int $repositoryCount,
        public readonly int $memberCount,
        public readonly \DateTimeInterface $updatedAt,
        public readonly \DateTimeInterface $createdAt,
    ) {}

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->title;
    }
}
