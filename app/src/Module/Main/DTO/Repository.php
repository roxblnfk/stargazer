<?php

declare(strict_types=1);

namespace App\Module\Main\DTO;

use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\Result\RepositoryInfo;

final class Repository implements \Stringable
{
    public readonly string $owner;
    public readonly string $name;

    public function __construct(
        public readonly int $id,
        public readonly int $ownerId,
        public readonly GithubRepository $fullName,
        public readonly bool $active,
        public readonly ?RepositoryInfo $info,
        public readonly \DateTimeInterface $updatedAt,
        public readonly \DateTimeInterface $createdAt,
    ) {
        $this->owner = $fullName->owner->name;
        $this->name = $fullName->name;
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return (string) $this->fullName;
    }
}
