<?php

declare(strict_types=1);

namespace App\Module\Github;

use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\Dto\GithubStargazer;
use App\Module\Github\Dto\GithubUser;
use App\Module\Github\Internal\TokenPool;
use App\Module\Github\Result\RepositoryInfo;
use Psr\Http\Client\ClientInterface;
use TypeLang\Mapper\Mapper;

/**
 * Service for interacting with GitHub API to retrieve repository stargazers.
 */
final class GithubService
{
    private const BASE_URI = 'https://api.github.com/';

    private readonly string|\Stringable $token;

    public function __construct(
        TokenPool $tokenPool,
        private readonly Mapper $mapper,
    ) {
        $this->token = $tokenPool->getToken();
    }

    /**
     * Get detailed repository information.
     */
    public function getRepositoryInfo(GithubRepository $repository): RepositoryInfo
    {
        $client = $this->createClient();

        $response = $client->request('GET', "repos/{$repository}");
        $data = \json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $this->mapper->denormalize($data, RepositoryInfo::class);
    }

    /**
     * Get the total number of stars for a given repository.
     *
     * @return int<0, max> The total number of stars
     */
    public function getStarsCount(GithubRepository $repository): int
    {
        $client = $this->createClient();

        $response = $client->request('GET', "repos/{$repository}");
        $data = \json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $data['stargazers_count'];
    }

    /**
     * @return iterable<int, \App\Module\Github\Dto\GithubStargazer>
     */
    public function getStargazers(GithubRepository $repository): iterable
    {
        $client = $this->createClient();
        $page = 1;
        $perPage = 100;

        do {
            $response = $client->request('GET', "repos/{$repository}/stargazers", [
                'query' => [
                    'page' => $page,
                    'per_page' => $perPage,
                ],
                'headers' => [
                    'Accept' => 'application/vnd.github.v3.star+json',
                ],
            ]);

            $data = \json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            foreach ($data as $starData) {
                yield new GithubStargazer(
                    repository: $repository,
                    user: new GithubUser($starData['user']['login']),
                    starredAt: new \DateTimeImmutable($starData['starred_at']),
                );
            }

            $hasMorePages = \count($data) === $perPage;
            $page++;
        } while ($hasMorePages);
    }

    /**
     * Create an authenticated HTTP client.
     */
    private function createClient(): ClientInterface
    {
        return new \GuzzleHttp\Client([
            'base_uri' => self::BASE_URI,
            'headers' => [
                'Authorization' => "token {$this->token}",
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'GitHub-Stars-App',
            ],
            'verify' => false,
        ]);
    }
}
