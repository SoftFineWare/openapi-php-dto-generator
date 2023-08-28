<?php

declare(strict_types=1);

namespace SoftFineWare\OpenapiGenerator\OpenApi;

use SoftFineWare\OpenapiGenerator\NamespaceHelper;

class RefFullClassNameConverter
{
    private NamespaceHelper $namespaceHelper;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(NamespaceHelper $namespaceHelper)
    {
        $this->namespaceHelper = $namespaceHelper;
    }

    public function convertRefToFullClassName(string $ref): string
    {
        $rest = substr($ref, strlen('#/components/'));
        $parts = explode('/', $rest);
        $parts = array_map('ucfirst', $parts);
        return $this->namespaceHelper->getClassReference($parts[0], $parts[1]);
    }
}
