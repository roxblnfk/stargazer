<?php

declare(strict_types=1);

namespace App\Feature\Profile;


use Spiral\Filters\Attribute\Input\Query;
use Spiral\Filters\Model\Filter;
use Spiral\Filters\Model\FilterDefinitionInterface;
use Spiral\Filters\Model\HasFilterDefinition;
use Spiral\Validator\FilterDefinition;

final class ProfileRequest extends Filter implements HasFilterDefinition
{
    #[Query]
    public string $username;

    public function filterDefinition(): FilterDefinitionInterface
    {
        return new FilterDefinition([
            'username' => ['required', 'string'],
        ]);
    }
}
