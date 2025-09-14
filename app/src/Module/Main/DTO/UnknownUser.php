<?php

declare(strict_types=1);

namespace App\Module\Main\DTO;

use App\Module\Github\Dto\GithubUser;

final class UnknownUser implements \Stringable
{
    public $info = null;

    public function __construct(
        public readonly GithubUser $login,
    ) {}

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return (string) $this->login;
    }
}
