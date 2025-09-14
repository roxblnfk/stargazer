<?php

declare(strict_types=1);

namespace App\Module\Main;

use App\Module\Github\Dto\GithubUser;
use App\Module\Main\DTO\UnknownUser;
use App\Module\Main\DTO\User;
use App\Module\Main\Internal\ORM\UserRepository;
use Spiral\Core\Attribute\Singleton;
use Spiral\Prototype\Traits\PrototypeTrait;

#[Singleton]
class UserService
{
    use PrototypeTrait;

    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function getByUsername(GithubUser $user): UnknownUser|User
    {
        return $this->userRepository->whereLogin($user)->findOne()?->toDTO() ?? new UnknownUser($user);
    }
}
