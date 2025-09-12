<?php

declare(strict_types=1);

namespace App\Module\Github;

use App\Module\Github\Dto\GithubRepository;
use App\Module\Github\Internal\TokenPool;
use App\Module\Github\Result\RepositoryInfo;
use App\Module\Github\Result\StargazerInfo;
use App\Module\Github\Result\UserInfo;
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
     * @return iterable<int, StargazerInfo>
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
                yield $this->mapper->denormalize($starData, StargazerInfo::class);
            }

            $hasMorePages = \count($data) === $perPage;
            $page++;
        } while ($hasMorePages);
    }

    /**
     * Get detailed user information by username.
     */
    public function getUserInfo(string $username): UserInfo
    {
        $client = $this->createClient();

        $response = $client->request('GET', "users/{$username}");
        $data = \json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $this->mapper->denormalize($data, UserInfo::class);
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
