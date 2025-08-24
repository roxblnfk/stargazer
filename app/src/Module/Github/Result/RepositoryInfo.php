<?php

declare(strict_types=1);

namespace App\Module\Github\Result;

use JsonSerializable;
use TypeLang\Mapper\Mapping\MapName;

/**
 * Data Transfer Object for GitHub repository information.
 */
final class RepositoryInfo implements JsonSerializable, \Stringable
{
    public function __construct(
        /** @var positive-int */
        public readonly int $id,

        /** @var non-empty-string */
        #[MapName('node_id')]
        public readonly string $nodeId,

        /** @var non-empty-string */
        public readonly string $name,

        /** @var non-empty-string */
        #[MapName('full_name')]
        public readonly string $fullName,
        public readonly bool $private,
        public readonly OwnerInfo $owner,

        /** @var non-empty-string */
        #[MapName('html_url')]
        public readonly string $htmlUrl,

        /** @var string|null */
        public readonly ?string $description,
        public readonly bool $fork,

        /** @var non-empty-string */
        public readonly string $url,
        #[MapName('created_at')]
        public readonly \DateTimeImmutable $createdAt,
        #[MapName('updated_at')]
        public readonly \DateTimeImmutable $updatedAt,
        #[MapName('pushed_at')]
        public readonly \DateTimeImmutable $pushedAt,

        /** @var non-empty-string */
        #[MapName('git_url')]
        public readonly string $gitUrl,

        /** @var non-empty-string */
        #[MapName('ssh_url')]
        public readonly string $sshUrl,

        /** @var non-empty-string */
        #[MapName('clone_url')]
        public readonly string $cloneUrl,

        /** @var non-empty-string */
        #[MapName('svn_url')]
        public readonly string $svnUrl,

        /** @var non-empty-string|null */
        public readonly ?string $homepage,

        /** @var int<0, max> */
        public readonly int $size,

        /** @var int<0, max> */
        #[MapName('stargazers_count')]
        public readonly int $stargazersCount,

        /** @var int<0, max> */
        #[MapName('watchers_count')]
        public readonly int $watchersCount,

        /** @var non-empty-string|null */
        public readonly ?string $language,
        #[MapName('has_issues')]
        public readonly bool $hasIssues,
        #[MapName('has_projects')]
        public readonly bool $hasProjects,
        #[MapName('has_downloads')]
        public readonly bool $hasDownloads,
        #[MapName('has_wiki')]
        public readonly bool $hasWiki,
        #[MapName('has_pages')]
        public readonly bool $hasPages,
        #[MapName('has_discussions')]
        public readonly bool $hasDiscussions,

        /** @var int<0, max> */
        #[MapName('forks_count')]
        public readonly int $forksCount,

        /** @var non-empty-string|null */
        #[MapName('mirror_url')]
        public readonly ?string $mirrorUrl,
        public readonly bool $archived,
        public readonly bool $disabled,

        /** @var int<0, max> */
        #[MapName('open_issues_count')]
        public readonly int $openIssuesCount,
        public readonly ?License $license,
        #[MapName('allow_forking')]
        public readonly bool $allowForking,
        #[MapName('is_template')]
        public readonly bool $isTemplate,
        #[MapName('web_commit_signoff_required')]
        public readonly bool $webCommitSignoffRequired,

        /** @var array<non-empty-string> */
        public readonly array $topics,

        /** @var non-empty-string */
        public readonly string $visibility,

        /** @var int<0, max> */
        public readonly int $forks,

        /** @var int<0, max> */
        #[MapName('open_issues')]
        public readonly int $openIssues,

        /** @var int<0, max> */
        public readonly int $watchers,

        /** @var non-empty-string */
        #[MapName('default_branch')]
        public readonly string $defaultBranch,
    ) {}

    public static function fromJsonArray(array $info): self
    {
        $info['owner'] = OwnerInfo::fromJsonArray($info['owner']);
        isset($info['license']) and $info['license'] = License::fromJsonArray($info['license']);
        $info['createdAt'] = new \DateTimeImmutable($info['createdAt']);
        $info['updatedAt'] = new \DateTimeImmutable($info['updatedAt']);
        $info['pushedAt'] = new \DateTimeImmutable($info['pushedAt']);
        return new self(...$info);
    }

    public static function fromJsonString(string $json): self
    {
        return self::fromJsonArray(\json_decode($json, true));
    }

    public function jsonSerialize(): array
    {
        $result = (array) $this;
        $result['createdAt'] = $this->createdAt->format(DATE_ATOM);
        $result['updatedAt'] = $this->updatedAt->format(DATE_ATOM);
        $result['pushedAt'] = $this->pushedAt->format(DATE_ATOM);
        return $result;
    }

    public function __toString(): string
    {
        return \json_encode($this, \JSON_UNESCAPED_UNICODE);
    }
}
