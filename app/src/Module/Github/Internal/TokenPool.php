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
    public function getNextToken(): \Stringable
    {
        // TODO: Implement token rotation logic.
        return GithubToken::findOne();
    }
}
