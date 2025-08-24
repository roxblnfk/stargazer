<?php

declare(strict_types=1);

namespace App\Module\Github\Result;

use TypeLang\Mapper\Mapping\MapName;

/**
 * Data Transfer Object for GitHub repository owner information.
 */
final class Owner
{
    public function __construct(
        /** @var non-empty-string */
        public readonly string $login,

        /** @var positive-int */
        public readonly int $id,

        /** @var non-empty-string */
        #[MapName('node_id')]
        public readonly string $nodeId,

        /** @var non-empty-string */
        #[MapName('avatar_url')]
        public readonly string $avatarUrl,

        /** @var string */
        #[MapName('gravatar_id')]
        public readonly string $gravatarId,

        /** @var non-empty-string */
        public readonly string $url,

        /** @var non-empty-string */
        #[MapName('html_url')]
        public readonly string $htmlUrl,

        /** @var non-empty-string */
        #[MapName('followers_url')]
        public readonly string $followersUrl,

        /** @var non-empty-string */
        #[MapName('following_url')]
        public readonly string $followingUrl,

        /** @var non-empty-string */
        #[MapName('gists_url')]
        public readonly string $gistsUrl,

        /** @var non-empty-string */
        #[MapName('starred_url')]
        public readonly string $starredUrl,

        /** @var non-empty-string */
        #[MapName('subscriptions_url')]
        public readonly string $subscriptionsUrl,

        /** @var non-empty-string */
        #[MapName('organizations_url')]
        public readonly string $organizationsUrl,

        /** @var non-empty-string */
        #[MapName('repos_url')]
        public readonly string $reposUrl,

        /** @var non-empty-string */
        #[MapName('events_url')]
        public readonly string $eventsUrl,

        /** @var non-empty-string */
        #[MapName('received_events_url')]
        public readonly string $receivedEventsUrl,

        /** @var non-empty-string */
        public readonly string $type,

        /** @var non-empty-string */
        #[MapName('user_view_type')]
        public readonly string $userViewType,
        #[MapName('site_admin')]
        public readonly bool $siteAdmin,
    ) {}
}
