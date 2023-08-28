<?php

declare(strict_types=1);

namespace SoftFineWare\OpenapiGenerator\MetaInfo;

/**
 * TODO have to be moved to package or be generated
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class MaxItems
{
    public function __construct(
        public int $value,
    ) {
    }
}
