<?php

declare(strict_types=1);

namespace App\Module\Github\Dto;

final class GithubRepository implements \Stringable
{
    /**
     * @param non-empty-string $name The repository name, e.g. "orm"
     */
    public function __construct(
        public readonly GithubOrganization $organization,
        public readonly string $name,
    ) {}

    public function __toString(): string
    {
        return "$this->organization/$this->name";
    }

    /**
     * Create a GithubRepository from a full name string like "organization/repo".
     *
     * @param non-empty-string $fullName The full repository name
     */
    public static function fromString(string $fullName): self
    {
        $parts = explode('/', $fullName, 2);
        \count($parts) === 2 or throw new \InvalidArgumentException("Invalid repository full name `$fullName`.");
        return new self(new GithubOrganization($parts[0]), $parts[1]);
    }

}
