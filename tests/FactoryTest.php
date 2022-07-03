<?php

namespace Valitor\ApiTest;

use ReflectionClass;
use Valitor\Exceptions\ClassDoesNotExistsException;
use Valitor\Factory;

class FactoryTest extends AbstractTest
{

    public function dataProvider(): array
    {
        $refClass = new ReflectionClass(Factory::class);
        $constants = $refClass->getConstants();
        $output = [];
        foreach ($constants as $class) {
            $output[] = [$class];
        }
        return $output;
    }

    /**
     * @dataProvider dataProvider
     */
    public function test_can_create(string $class)
    {
        $this->assertInstanceOf($class, Factory::create($class, $this->getAuth()));
    }

    public function test_does_not_exists()
    {
        $this->setExpectedException(ClassDoesNotExistsException::class);
        Factory::create('Foo\Bar', $this->getAuth());
    }

    public function test_does_not_exists_exception_catch()
    {
        try {
            Factory::create('Foo\Bar', $this->getAuth());
        } catch (ClassDoesNotExistsException $e) {
            $this->assertEquals('Foo\Bar', $e->getClass());
        }
    }

}
