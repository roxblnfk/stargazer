<?php

declare(strict_types=1);

namespace App\Module\Main;

use App\Module\Main\Internal\ORM\UserEntity;
use App\Module\Main\Internal\ORM\UserRepository;
use App\Module\Github\Result\UserInfo;
use Spiral\Core\Attribute\Singleton;
use Spiral\Prototype\Traits\PrototypeTrait;

#[Singleton]
class UserService
{
    use PrototypeTrait;

    public function __construct(
        private readonly UserRepository $repoRepository,
    ) {}

    public function getByUsername(string $username): ?UserEntity
    {
        return $this->repoRepository->whereLogin($username)->findOne();
    }

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
