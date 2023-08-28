<?php

declare(strict_types=1);

namespace SoftFineWare\OpenapiGenerator\OpenApi\ComponentsExtractors;

use Exception;
use SoftFineWare\OpenapiGenerator\Deffenition\ClassDefinitionData;
use SoftFineWare\OpenapiGenerator\OpenApi\DefinitionExtractor\ObjectDefinitionExtractor;

class ResponseExtractor
{
    private ObjectDefinitionExtractor $objectDefinitionExtractor;

    public function __construct(ObjectDefinitionExtractor $objectDefinitionExtractor)
    {
        $this->objectDefinitionExtractor = $objectDefinitionExtractor;
    }

    /**
     * @throws Exception
     */
    public function extractResponse(string $responseName, array $response): ClassDefinitionData
    {
        [
            'description' => $description,
            'content' => [
                'application/json' => [
                    'schema' => $schema,
                ],
            ],
        ] = $response;
        if ($schema['type'] !== 'object') {
            throw new Exception(sprintf('Type "%s" is not implemented', $schema['type']));
        }
        [
            'properties' => $properties,
        ] = $schema;
        /**
         * @var string|null $description
         * @var array<string, array> $properties
         */

        return $this->objectDefinitionExtractor->extractClassesDefinition($responseName, 'Responses', $description, $properties);
    }
}
