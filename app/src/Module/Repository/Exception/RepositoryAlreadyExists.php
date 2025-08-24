<?php

declare(strict_types=1);

namespace App\Module\Repository\Exception;

use App\Module\Github\Dto\GithubRepository;

final class RepositoryAlreadyExists extends \RuntimeException
{
    public function __construct(private readonly GithubRepository $repository)
    {
        parent::__construct("Repository '{$repository}' already registered.");
    }

    public function getRepository(): GithubRepository
    {
        return $this->repository;
    }
}
