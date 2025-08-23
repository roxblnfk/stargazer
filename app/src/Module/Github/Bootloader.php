<?php

declare(strict_types=1);

namespace App\Module\Github;

use Github\Api;
use Github\Api\AbstractApi as AA;
use Spiral\Boot\Bootloader\Bootloader as SpiralBootloader;

final class Bootloader extends SpiralBootloader
{
    public function defineBindings(): array
    {
        $createFactory = static fn(string $alias): \Closure => static fn(GithubService $service): AA => $service
            ->getApi($alias);
        return [
            Api\Repo::class => $createFactory('repo'),
            Api\CurrentUser::class => $createFactory('currentUser'),
            Api\Enterprise::class => $createFactory('enterprise'),
            Api\Miscellaneous\CodeOfConduct::class => $createFactory('codeOfConduct'),
            Api\Miscellaneous\Emojis::class => $createFactory('emojis'),
            Api\Miscellaneous\Licenses::class => $createFactory('licenses'),
            Api\Miscellaneous\Gitignore::class => $createFactory('gitignore'),
            Api\GitData::class => $createFactory('gitData'),
            Api\Gists::class => $createFactory('gists'),
            Api\Apps::class => $createFactory('apps'),
            Api\Issue::class => $createFactory('issues'),
            Api\Markdown::class => $createFactory('markdown'),
            Api\Notification::class => $createFactory('notifications'),
            Api\Organization::class => $createFactory('organizations'),
            Api\Organization\Projects::class => $createFactory('organizationProjects'),
            Api\Organization\OutsideCollaborators::class => $createFactory('outsideCollaborators'),
            Api\Organization\Members::class => $createFactory('members'),
            Api\Organization\Teams::class => $createFactory('teams'),
            Api\PullRequest::class => $createFactory('pullRequests'),
            Api\RateLimit::class => $createFactory('rateLimit'),
            Api\Search::class => $createFactory('search'),
            Api\User::class => $createFactory('users'),
            Api\Authorizations::class => $createFactory('authorizations'),
            Api\Meta::class => $createFactory('meta'),
            Api\GraphQL::class => $createFactory('graphql'),
            Api\Deployment::class => $createFactory('deployments'),
            Api\Copilot\Usage::class => $createFactory('copilotUsage'),
        ];
    }
}
