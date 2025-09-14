<?php

declare(strict_types=1);

namespace App\Module\Github\Dto;

final class GithubUser implements \Stringable
{
    /**
     * @param non-empty-string $name The user name, e.g. "johndoe"
     */
    public function __construct(
        public readonly string $name,
    ) {}

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return "$this->name";
    }
}
