<?php

declare(strict_types=1);

namespace App\Module\Github\Dto;

final class GithubOwner implements \Stringable
{
    /**
     * @param non-empty-string $name The organization name, e.g. "spiral"
     */
    public function __construct(
        public readonly string $name,
    ) {}

    public function __toString(): string
    {
        return $this->name;
    }
}
