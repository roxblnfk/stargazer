<?php

declare(strict_types=1);

namespace App\Module\Github\Dto;

/**
 * A link between a user and a repository they have starred.
 */
final class GithubStargazer
{
    public function __construct(
        public readonly GithubRepository $repository,
        public readonly GithubUser $user,
        public readonly \DateTimeImmutable $starredAt,
    ) {}
}
