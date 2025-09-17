<?php

declare(strict_types=1);

namespace App\Module\Github\Internal;

use Cycle\ORM\Select\QueryBuilder;

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
    public function getNextToken(): \Stringable|string
    {
        # Get most recently used token
        $token = GithubTokenEntity::query()
            ->where(static function (QueryBuilder $select): void {
                $select
                    ->where('expiresAt', '>', new \DateTimeImmutable())
                    ->orWhere('expiresAt', '=', null);
            })
            ->orderBy('usedAt', 'ASC')
            ->fetchOne();

        $token->usedAt = new \DateTimeImmutable();
        $token->save();

        return $token;
    }
}
