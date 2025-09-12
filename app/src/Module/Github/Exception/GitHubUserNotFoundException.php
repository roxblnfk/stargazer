<?php

declare(strict_types=1);

namespace App\Module\Github\Exception;

final class GitHubUserNotFoundException extends \Exception
{
    public function __construct(string $username)
    {
        parent::__construct("GitHub user '{$username}' not found");
    }
}