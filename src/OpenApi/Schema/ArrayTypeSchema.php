<?php

namespace SoftFineWare\OpenapiGenerator\OpenApi\Schema;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

class ArrayTypeSchema
{
    public function build(array $schema):ClassType
    {
        $className = array_key_first($schema);

        // TODO need a way to resolve Pet class with namespace info
        $ref  = $schema[$className]['items']['$ref'];
        $maxItemValue = $schema[$className]['maxItems'];
        $itemType = str_replace('#/components/schemas/', '', $ref);
        // END

        $namespace = new PhpNamespace('HardcodeName');
        $arrayType = new ClassType($className, $namespace);
        $arrayType->addImplement(\Iterator::class);
        $arrayType
            ->addProperty('position', 0)
            ->setType('int')
            ->setPrivate()
        ;

        // TODO this part is optional and should be extensible
        $arrayType
            ->addAttribute(\SoftFineWare\OpenapiGenerator\MetaInfo\MaxItems::class, [$maxItemValue]);


        $constructor = $arrayType->addMethod('__construct')
            ->setReturnType('void');
        $constructor
            ->addPromotedParameter('container')
            ->setPrivate()
            ->setReadOnly()
            ->setType('array')
            ->addComment("@var list<$itemType>")
        ;

        $arrayType
            ->addMethod('rewind')
            ->setReturnType('void')
            ->addBody('$this->position = 0;');

        $arrayType
            ->addMethod('current')
            ->setReturnType($itemType)
            ->addBody('return $this->container[$this->position];');

        $arrayType
            ->addMethod('key')
            ->setReturnType('int')
            ->addBody('return $this->position;');

        $arrayType
            ->addMethod('next')
            ->setReturnType('void')
            ->addBody('++$this->position;');


        $arrayType
            ->addMethod('valid')
            ->setReturnType('bool')
            ->addBody('return isset($this->container[$this->position]);');


        return $arrayType;
    }
}