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
}
