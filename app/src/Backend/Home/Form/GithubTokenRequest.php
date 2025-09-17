<?php

declare(strict_types=1);

namespace App\Backend\Home\Form;

use Spiral\Filters\Attribute\Input\Post;
use Spiral\Filters\Attribute\Setter;
use Spiral\Filters\Model\Filter;
use Spiral\Filters\Model\FilterDefinitionInterface;
use Spiral\Filters\Model\HasFilterDefinition;
use Spiral\Validator\FilterDefinition;

final class GithubTokenRequest extends Filter implements HasFilterDefinition
{
    #[Post]
    #[Setter(filter: 'trim')]
    public string $token;

    #[Post(key: 'expires_at')]
    public ?\DateTimeImmutable $expiresAt = null;

    public function filterDefinition(): FilterDefinitionInterface
    {
        return new FilterDefinition([
            'token' => [
                'required',
                'string',
                ['string::longer', 64],
            ],
            'expiresAt' => [
                'datetime::valid',
            ],
        ]);
    }
}
