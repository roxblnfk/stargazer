<?php

declare(strict_types=1);

namespace App\Backend\Home\Form;

use Spiral\Filters\Attribute\Input\Query;
use Spiral\Filters\Model\Filter;
use Spiral\Filters\Model\FilterDefinitionInterface;
use Spiral\Filters\Model\HasFilterDefinition;
use Spiral\Validator\FilterDefinition;

final class TokenRequest extends Filter implements HasFilterDefinition
{
    #[Query]
    public string $token;

    public function filterDefinition(): FilterDefinitionInterface
    {
        return new FilterDefinition([
            'token' => ['required', 'string'],
        ]);
    }
}
