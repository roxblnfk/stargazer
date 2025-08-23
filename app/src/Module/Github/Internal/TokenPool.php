<?php

declare(strict_types=1);

namespace App\Module\Github\Internal;

/**
 * Github token pool.
 *
 * Contains logic to manage and rotate multiple tokens to avoid rate limiting.
 */
final class TokenPool
{
    /**
     * Get a GitHub token from the pool.
     *
     * @return \Stringable|non-empty-string A GitHub token
     */
    public function getToken(): \Stringable|string
    {
        $envToken = \getenv('GITHUB_TOKEN');
        if (\is_string($envToken) && $envToken !== '') {
            return $envToken;
        }

        // TODO: Implement token rotation logic.
        return GithubToken::findOne();
    }
}
