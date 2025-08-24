<?php

declare(strict_types=1);

namespace App\Module\Github\Result;

use TypeLang\Mapper\Mapping\MapName;

/**
 * Data Transfer Object for GitHub repository license information.
 */
final class License
{
    public function __construct(
        /** @var non-empty-string */
        public readonly string $key,

        /** @var non-empty-string */
        public readonly string $name,

        /** @var non-empty-string */
        #[MapName('spdx_id')]
        public readonly string $spdxId,

        /** @var non-empty-string */
        public readonly string $url,

        /** @var non-empty-string */
        #[MapName('node_id')]
        public readonly string $nodeId,
    ) {}
}
