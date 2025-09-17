<?php

declare(strict_types=1);

namespace App\Module\Campaign\Form;

use Ramsey\Uuid\UuidInterface;
use Spiral\Filters\Attribute\Input\Post;
use Spiral\Filters\Attribute\Setter;
use Spiral\Validator\FilterDefinition;

final class UpdateCampaign extends CreateCampaign
{
    #[Post]
    public UuidInterface $uuid;

    #[Post]
    #[Setter(filter: 'boolval')]
    public bool $visible = false;

    #[Post(key: 'invite_code')]
    #[Setter(filter: 'trim')]
    public ?string $inviteCode = null;

    #[Post(key: 'old_stars_coefficient')]
    public float $oldStarsCoefficient = 1.0;

    public function filterDefinition(): FilterDefinition
    {
        $parentDefinition = parent::filterDefinition();

        return new FilterDefinition(
            \array_merge(
                $parentDefinition->validationRules(),
                [
                    'visible' => [
                        // HTML checkbox sends "1" or nothing, so we need to handle string to bool conversion
                        'boolean',
                    ],
                    'startedAt' => [
                        'required',
                        'datetime::valid',
                        // No 'datetime::future' for update - allow past dates
                    ],
                    'inviteCode' => [
                        'string',
                        ['string::shorter', 65], // Entity field has length 64, so max 64 characters
                    ],
                    'oldStarsCoefficient' => [
                        'required',
                        'numeric',
                        ['number::higher', -100], // Must be positive
                        ['number::lower', 100], // Max 99.99 as per form constraint
                    ],
                ],
            ),
            $parentDefinition->mappingSchema(),
        );
    }
}
