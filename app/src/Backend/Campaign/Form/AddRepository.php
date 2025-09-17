<?php

declare(strict_types=1);

namespace App\Backend\Campaign\Form;

use Ramsey\Uuid\UuidInterface;
use Spiral\Filters\Attribute\Input\Post;
use Spiral\Filters\Model\Filter;

class AddRepository extends Filter
{
    #[Post]
    public UuidInterface $campaignUuid;

    #[Post]
    public string $repository;
}
