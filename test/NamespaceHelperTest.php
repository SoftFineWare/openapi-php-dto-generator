<?php

declare(strict_types=1);

namespace Test;

use PHPUnit\Framework\Attributes\CoversClass;
use SoftFineWare\OpenapiGenerator\NamespaceHelper;
use PHPUnit\Framework\TestCase;

#[CoversClass(NamespaceHelper::class)]
class NamespaceHelperTest extends TestCase
{
    private NamespaceHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new NamespaceHelper('Foo');
    }

    public function testGetNamespace(): void
    {
        $this->assertSame(
            "Foo\Bar",
            $this->helper->getNamespace('Bar')
        );
    }

    public function testSetGlobalNamespace(): void
    {
        $this->helper->setGlobalNamespace('Bas');
        $this->assertSame(
            "Bas\Bar",
            $this->helper->getNamespace('Bar')
        );
    }

    public function testGetClassReference(): void
    {
        $this->assertSame(
            "Foo\Bar\Bas",
            $this->helper->getClassReference('Bar', 'Bas')
        );
    }
}
