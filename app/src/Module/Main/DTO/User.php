<?php

declare(strict_types=1);

namespace App\Module\Main\DTO;

use App\Module\Github\Dto\GithubUser;
use App\Module\Github\Result\UserInfo;

final class User implements \Stringable
{
    public function __construct(
        public readonly int $id,
        public readonly GithubUser $login,
        public readonly ?UserInfo $info,
        public readonly \DateTimeInterface $updatedAt,
        public readonly \DateTimeInterface $createdAt,
    ) {}

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return (string) $this->login;
    }
}
