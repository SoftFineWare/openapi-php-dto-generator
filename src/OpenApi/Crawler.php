<?php

declare(strict_types=1);

namespace SoftFineWare\OpenapiGenerator\OpenApi;

use SoftFineWare\OpenapiGenerator\Deffenition\ClassDefinitionData;
use SoftFineWare\OpenapiGenerator\OpenApi\ComponentsExtractors\RequestBodyExtractor;
use SoftFineWare\OpenapiGenerator\OpenApi\ComponentsExtractors\ResponseExtractor;
use SoftFineWare\OpenapiGenerator\OpenApi\ComponentsExtractors\SchemaExtractor;
use Psr\Log\LoggerInterface;

class Crawler
{
    private RequestBodyExtractor $requestBodyExtractor;
    private SchemaExtractor $schemaExtractor;
    private ResponseExtractor $responseExtractor;
    private LoggerInterface $logger;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(RequestBodyExtractor $requestBodyExtractor, SchemaExtractor $schemaExtractor, ResponseExtractor $responseExtractor, LoggerInterface $logger)
    {
        $this->requestBodyExtractor = $requestBodyExtractor;
        $this->schemaExtractor = $schemaExtractor;
        $this->responseExtractor = $responseExtractor;
        $this->logger = $logger;
    }

    /**
     * @psalm-suppress MixedAssignment
     * @psalm-return \Traversable<int, ClassDefinitionData>
     */
    public function walk(array $openapi): \Traversable
    {
        [
            'components' => [
//            'responses' => $responses,
            'schemas' => $schemas,
//            'requestBodies' => $requestBodies,
        ]] = $openapi;
        /**
         * @var array<string, array> $requestBodies
         * @var array<string, array> $responses
         * @var array<string, array> $schemas
         */

        $this->logger->debug('Extracting definition from components');
        // Components
        foreach ($schemas as $schemaName => $schema) {
            yield $this->schemaExtractor->extractSchema($schemaName, $schema);
        }

//        foreach ($responses as $responseName => $responseStructure) {
//            yield $this->responseExtractor->extractResponse($responseName, $responseStructure);
//        }
//
//        foreach ($requestBodies as $requestBodyName => $requestBody) {
//            try {
//                yield $this->requestBodyExtractor->extractResponseBody($requestBodyName, $requestBody, $openapi);
//            } catch (\Exception $exception) {
//                $this->logger->error('Cannot create RequestBody "{requestBodyName}"', [
//                    'requestBodyName' => $requestBodyName,
//                    'requestBody' => $requestBody,
//                    'exception' => (string) $exception,
//                ]);
//            }
//        }
    }
}
