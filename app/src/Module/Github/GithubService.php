<?php

declare(strict_types=1);

namespace App\Module\Github;

use Cycle\ActiveRecord\Bridge\Spiral\Bootloader\ActiveRecordBootloader;
use Github\Api\AbstractApi;
use Github\Client;
use Psr\Http\Client\ClientInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Cycle\Bootloader as CycleBridge;
use Spiral\Cycle\Bootloader\DataGridBootloader;

final class GithubService
{
    public function __construct(
        private readonly ClientInterface $httpClient,
    ) {}

    public function getApi(string $name): AbstractApi
    {
        $client = Client::createWithHttpClient($this->httpClient);
        // $client->addCache();
        return $client->api($name);
    }
}
