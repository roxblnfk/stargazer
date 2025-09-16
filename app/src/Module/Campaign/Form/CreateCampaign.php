<?php

declare(strict_types=1);

namespace App\Module\Campaign\Form;

use Spiral\Filters\Attribute\Input\Post;
use Spiral\Filters\Attribute\Setter;
use Spiral\Filters\Model\Filter;
use Spiral\Filters\Model\HasFilterDefinition;
use Spiral\Validator\FilterDefinition;

class CreateCampaign extends Filter implements HasFilterDefinition
{
    #[Post]
    #[Setter(filter: 'trim')]
    public string $title = '';

    #[Post]
    #[Setter(filter: 'trim')]
    public string $description = '';

    #[Post(key: 'started_at')]
    public ?\DateTimeImmutable $startedAt = null;

    #[Post(key: 'finished_at')]
    public ?\DateTimeImmutable $finishedAt = null;

    public function filterDefinition(): FilterDefinition
    {
        return new FilterDefinition([
            'title' => [
                'required',
                'string',
                ['string::shorter', 255],
            ],
            'description' => [
                'string',
                ['string::shorter', 64000],
            ],
            'startedAt' => [
                'required',
                'datetime::valid',
                ['datetime::future', 'orNow' => false],
            ],
            'finishedAt' => [
                'datetime::valid',
            ],
        ]);
    }
}
