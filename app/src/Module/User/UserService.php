<?php

declare(strict_types=1);

namespace App\Module\User;

use App\Module\Github\Result\UserInfo;
use App\Module\User\Internal\UserEntity;
use App\Module\User\Internal\UserRepository;
use Spiral\Core\Attribute\Singleton;
use Spiral\Prototype\Traits\PrototypeTrait;

#[Singleton]
class UserService
{
    use PrototypeTrait;

    public function __construct(
        private readonly UserRepository $repoRepository,
    ) {}

    public function getOrCreate(UserInfo $info): UserEntity
    {
        $found = $this->repoRepository->findByPK($info->id);
        if ($found !== null) {
            return $found;
        }

        $user = UserEntity::createFromOwnerInfo($info);
        $user->saveOrFail();
        return $user;
    }
}
