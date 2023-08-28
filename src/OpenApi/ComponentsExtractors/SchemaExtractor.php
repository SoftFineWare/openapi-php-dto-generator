<?php

declare(strict_types=1);

namespace SoftFineWare\OpenapiGenerator\OpenApi\ComponentsExtractors;

use Exception;
use SoftFineWare\OpenapiGenerator\Deffenition\ClassDefinitionData;
use SoftFineWare\OpenapiGenerator\NamespaceHelper;
use SoftFineWare\OpenapiGenerator\OpenApi\DefinitionExtractor\PropertyExtractor;
use SoftFineWare\OpenapiGenerator\PHP\ConstructorGenerator;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;

class SchemaExtractor
{
    private const SUB_NAMESPACE = 'Schemas';

    private const SUPPORTED_TYPES = ['object', 'array'];

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly ConstructorGenerator $constructorGenerator,
        private readonly NamespaceHelper      $namespaceHelper,
        private readonly PropertyExtractor    $propertyExtractor
    ) {
    }

    /**
     * @throws Exception
     */
    public function extractSchema(string $schemaName, array $schema): ClassDefinitionData
    {
        [
            'type' => $type,
            'required' => $required,
            'description' => $description,
            'properties' => $properties
        ] = $schema;
        /**
         * @var list<string> $required
         * @var array<string, array> $properties
         * @var string|null $description
         * @var string $type
         */
        if (!in_array($type, self::SUPPORTED_TYPES)) {
            throw new Exception(sprintf('Type "%s" is not implemented. Schema name: %s', $type, $schemaName));
        }
        if ($type === 'array') {


        }

        $class = new ClassType($schemaName);
        if ($description) {
            $class->addComment($description);
        }
        $construct = new Method('__construct');
        $construct->setPublic();
        $constructorProperties = [];
        foreach ($properties as $propertyName => $propertyStructure) {
            $property = $this->propertyExtractor->extractProperty($propertyName, $propertyStructure);
            if (in_array($property->getName(), $required)) {
                $constructorProperties[] = $property;
            }
            $class->addMember($property);
        }
        if (!empty($constructorProperties)) {
            $this->constructorGenerator->addContractorWithRequiredArgument($class, $constructorProperties);
        }
        return new ClassDefinitionData($class, $this->namespaceHelper->getNamespace(self::SUB_NAMESPACE), self::SUB_NAMESPACE);
    }
}
