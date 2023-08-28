<?php

declare(strict_types=1);

namespace SoftFineWare\OpenapiGenerator\OpenApi\ComponentsExtractors;

use Exception;
use SoftFineWare\OpenapiGenerator\ArrayPathFetcher;
use SoftFineWare\OpenapiGenerator\Deffenition\ClassDefinitionData;
use SoftFineWare\OpenapiGenerator\NamespaceHelper;
use SoftFineWare\OpenapiGenerator\OpenApi\DefinitionExtractor\PropertyExtractor;
use SoftFineWare\OpenapiGenerator\OpenApi\RefData;
use SoftFineWare\OpenapiGenerator\PHP\ConstructorGenerator;
use Nette\PhpGenerator\ClassType;
use Psr\Log\LoggerInterface;

class RequestBodyExtractor
{
    private const NAMESPACE = 'RequestBodies';
    private ArrayPathFetcher $path;
    private LoggerInterface $logger;
    private ArrayPathFetcher $fetcher;
    private NamespaceHelper $namespaceHelper;
    private PropertyExtractor $propertyExtractor;
    private ConstructorGenerator $constructorGenerator;

    public function __construct(ArrayPathFetcher $path, LoggerInterface $logger, ArrayPathFetcher $fetcher, NamespaceHelper $namespaceHelper, PropertyExtractor $propertyExtractor, ConstructorGenerator $constructorGenerator)
    {
        $this->path = $path;
        $this->logger = $logger;
        $this->fetcher = $fetcher;
        $this->namespaceHelper = $namespaceHelper;
        $this->propertyExtractor = $propertyExtractor;
        $this->constructorGenerator = $constructorGenerator;
    }

    /**
     * @throws Exception
     */
    public function extractResponseBody(string $requestBodyName, array $requestBody, array $openapi): ClassDefinitionData
    {
        ['description' => $description, 'content' => ['application/json' => [
            'schema' => [
                '$ref' => $ref,
            ],
        ]]] = $requestBody;
        if (!is_string($ref)) {
            throw new Exception('Creating response body without ref to schema is not supported');
        }
        $ref = new RefData($ref);
        $data = $this->fetcher->getContent($ref->path, $openapi);

        $class = new ClassType();
        if ($description) {
            $class->addComment($description);
        }
        $required = $data['required'];
        $properties = [];
        $constructorProperties = [];
        foreach ($data['properties'] ?? [] as $propertyName => $propertyData) {
            if ($propertyData['readOnly'] ?? false) {
                continue;
            }
            $property = $this->propertyExtractor->extractProperty($propertyName, $propertyData);

            if (in_array($propertyName, $required, true)) {
                $constructorProperties[] = $property;
            }
            $class->addMember($property);
        }

        if (!empty($constructorProperties)) {
            $this->constructorGenerator->addContractorWithRequiredArgument($class, $constructorProperties);
        }

        $class->setName($requestBodyName);
        return  new ClassDefinitionData(
            $class,
            $this->namespaceHelper->getNamespace(self::NAMESPACE),
            self::NAMESPACE
        );
    }
}
