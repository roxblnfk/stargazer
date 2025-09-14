<?php

declare(strict_types=1);

namespace App\Feature\Profile;

use App\Module\Main\UserService;
use App\Module\Github\Exception\GitHubUserNotFoundException;
use App\Module\Github\GithubService;
use App\Module\Github\Result\UserInfo;
use Spiral\Core\Attribute\Singleton;

#[Singleton]
final class ProfileService
{
    public function __construct(
        private readonly UserService $userService,
        private readonly GithubService $githubService,
    ) {}

    /**
     * @throws GitHubUserNotFoundException
     */
    public function getInfo(string $username): UserInfo
    {
        $user = $this->userService->getByUsername($username);

        if ($user === null) {
            try {
                $info = $this->githubService->getUserInfo($username);
                $user = $this->userService->getOrCreate($info);
            } catch (\Throwable) {
                throw new GitHubUserNotFoundException($username);
            }
        }

        return $user->info;
    }
}
