<?php

declare(strict_types=1);

namespace App\Module\Data\DTO;

use App\Module\Github\Result\RepositoryInfo;

final class Repository
{
    public function __construct(
        public readonly int $id,
        public readonly string $owner,
        public readonly int $ownerId,
        public readonly string $name,
        public readonly bool $active,
        public readonly ?RepositoryInfo $info,
        public readonly \DateTimeInterface $updatedAt,
        public readonly \DateTimeInterface $createdAt,
    ) {}

    public function getFullName(): string
    {
        return "{$this->owner}/{$this->name}";
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
