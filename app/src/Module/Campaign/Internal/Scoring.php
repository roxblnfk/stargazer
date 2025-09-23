<?php

declare(strict_types=1);

namespace App\Module\Campaign\Internal;

use App\Application\ORM\ActiveRecord;
use App\Module\Campaign\Internal\ORM\CampaignEntity;
use App\Module\Campaign\Internal\ORM\CampaignRepoEntity;
use App\Module\Campaign\Internal\ORM\CampaignUserEntity;
use App\Module\Main\Internal\ORM\StarEntity;
use Cycle\Database\DatabaseInterface;
use Ramsey\Uuid\UuidInterface;
use Spiral\Core\Attribute\Singleton;

#[Singleton]
final class Scoring
{
    /**
     * Recalculate scores for all repositories in the campaign.
     */
    public function calculateScores(UuidInterface $campaignUuid): void
    {
        ActiveRecord::transact(static function (DatabaseInterface $db) use ($campaignUuid): void {
            $tCampaign = CampaignEntity::tableName();
            $tCampaignRepos = CampaignRepoEntity::tableName();
            $tCampaignUsers = CampaignUserEntity::tableName();
            $tStargazer = StarEntity::tableName();

            # Calculate stars per user per repository
            # Updates campaign_user.score by calculating total points from user's starred repositories
            # Points = sum of repository scores with 1.2x multiplier for stars before campaign start
            # Only counts stars with non-null starred_at timestamp within campaign timeframe
            $db->execute(<<<SQL
                UPDATE $tCampaignUsers
                SET score = COALESCE(user_stats.total_score, 0),
                    stars = COALESCE(user_stats.total_stars, 0)
                FROM (
                    SELECT
                        cu.campaign_uuid,
                        cu.user_id,
                        SUM(
                            CASE
                                WHEN s.starred_at < c.started_at THEN cr.score * c.old_stars_coefficient
                                ELSE cr.score * 1.0
                            END
                        ) as total_score,
                        COUNT(
                            CASE
                                WHEN s.starred_at >= c.started_at THEN 1
                                ELSE NULL
                            END
                        ) as total_stars
                    FROM $tCampaignUsers cu
                    JOIN $tCampaign c ON c.uuid = cu.campaign_uuid
                    JOIN $tStargazer s ON s.user_id = cu.user_id
                    JOIN $tCampaignRepos cr ON cr.repo_id = s.repo_id AND cr.campaign_uuid = cu.campaign_uuid
                    WHERE
                        s.starred_at IS NOT NULL
                        AND ( c.finished_at IS NULL OR s.starred_at <= c.finished_at )
                    GROUP BY cu.campaign_uuid, cu.user_id
                ) user_stats
                WHERE
                    $tCampaignUsers.campaign_uuid = user_stats.campaign_uuid
                    AND $tCampaignUsers.user_id = user_stats.user_id;
                SQL);

            # Update repository stats
            $db->execute(<<<SQL
                UPDATE $tCampaignRepos
                SET
                    count_stars_at_all = COALESCE(repo_stats.stars_all, 0),
                    count_stars = COALESCE(repo_stats.stars_members, 0)
                FROM (
                    SELECT
                        cr.campaign_uuid,
                        cr.repo_id,
                        -- Все звёзды репозитория за период кампании
                        COUNT(s.user_id) as stars_all,
                        -- Звёзды только от участников кампании
                        COUNT(cu.user_id) as stars_members
                    FROM $tCampaignRepos cr
                    JOIN $tCampaign c ON c.uuid = cr.campaign_uuid
                    JOIN $tStargazer s ON s.repo_id = cr.repo_id
                    LEFT JOIN $tCampaignUsers cu ON cu.campaign_uuid = cr.campaign_uuid
                                               AND cu.user_id = s.user_id
                    WHERE
                        s.starred_at IS NOT NULL
                        AND s.starred_at >= c.started_at
                        AND (
                            c.finished_at IS NULL
                            OR s.starred_at <= c.finished_at
                        )
                    GROUP BY cr.campaign_uuid, cr.repo_id
                ) repo_stats
                WHERE
                    $tCampaignRepos.campaign_uuid = repo_stats.campaign_uuid
                    AND $tCampaignRepos.repo_id = repo_stats.repo_id
                SQL);

            # Updates campaign statistics:
            # count_users: number of unique users participating in the campaign
            # count_repos: number of unique repositories included in the campaign
            # count_stars: number of unique stars given by campaign participants to campaign repositories during the active campaign period only
            $db->execute(<<<SQL
                UPDATE $tCampaign
                SET
                    count_users = COALESCE(stats.user_count, 0),
                    count_repos = COALESCE(stats.repo_count, 0),
                    count_stars = COALESCE(stats.star_count, 0)
                FROM (
                     SELECT
                         c.uuid as campaign_uuid,
                         COUNT(DISTINCT cu.user_id) as user_count,
                         COUNT(DISTINCT cr.repo_id) as repo_count,
                         COUNT(DISTINCT CASE WHEN s.starred_at IS NOT NULL
                            AND s.starred_at >= c.started_at
                            AND (c.finished_at IS NULL OR s.starred_at <= c.finished_at)
                            THEN s.user_id || '_' || s.repo_id END) as star_count
                     FROM $tCampaign c
                              LEFT JOIN $tCampaignUsers cu ON cu.campaign_uuid = c.uuid
                              LEFT JOIN $tCampaignRepos cr ON cr.campaign_uuid = c.uuid
                              LEFT JOIN $tStargazer s ON s.repo_id = cr.repo_id
                         AND EXISTS (
                             SELECT 1 FROM $tCampaignUsers cu2
                             WHERE cu2.campaign_uuid = c.uuid
                               AND cu2.user_id = s.user_id
                         )
                     GROUP BY c.uuid
                 ) stats
                WHERE $tCampaign.uuid = stats.campaign_uuid
                SQL);
        });
    }
}
