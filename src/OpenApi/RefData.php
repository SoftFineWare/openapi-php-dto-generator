<?php

declare(strict_types=1);

namespace SoftFineWare\OpenapiGenerator\OpenApi;

/**
 * @psalm-immutable
 */
class RefData
{
    public string $path;

    public function __construct(string $ref)
    {
        $this->path = str_replace('/', '.', substr($ref, 2));
    }
}
