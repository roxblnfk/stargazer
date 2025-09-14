<?php

declare(strict_types=1);

namespace App\Module\User\Internal;

use App\Application\ORM\BaseRepository;

/**
 * @extends BaseRepository<UserEntity>
 */
final class UserRepository extends BaseRepository
{
    public function whereLogin(string|\Stringable $login): static
    {
        $clone = clone $this;
        $clone->select->where(['login' => $login]);
        return $clone;
    }
}
