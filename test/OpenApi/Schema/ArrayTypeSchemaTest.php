<?php

declare(strict_types=1);

namespace Test\OpenApi\Schema;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SoftFineWare\OpenapiGenerator\OpenApi\Schema\ArrayTypeSchema;

#[CoversClass(ArrayTypeSchema::class)]
class ArrayTypeSchemaTest extends TestCase
{
    private ArrayTypeSchema $schema;

    protected function setUp(): void
    {
        $this->schema = new ArrayTypeSchema();
        parent::setUp();
    }

    public static function cases(): \Generator
    {
        yield [
            [
                'Pets' => [
                    'type' => 'array',
                    'maxItems' => 100,
                    'items' => [
                        '$ref' => '#/components/schemas/Pet',
                    ],
                ],
            ],
            <<<'PHP'
#[\SoftFineWare\OpenapiGenerator\MetaInfo\MaxItems(100)]
class Pets implements \Iterator
{
	private int $position = 0;


	public function __construct(
		/** @var list<Pet> */
		private readonly array $container,
	): void
	{
	}


	public function rewind(): void
	{
		$this->position = 0;
	}


	public function current(): \Pet
	{
		return $this->container[$this->position];
	}


	public function key(): int
	{
		return $this->position;
	}


	public function next(): void
	{
		++$this->position;
	}


	public function valid(): bool
	{
		return isset($this->container[$this->position]);
	}
}

PHP,

        ];
    }

    #[DataProvider('cases')]
    #[Test]
    public function build(array $schema, string $expectedClassContent): void
    {
        $result = $this->schema->build($schema);
        self::assertSame(
            $expectedClassContent,
            (string)$result
        );
    }
}
