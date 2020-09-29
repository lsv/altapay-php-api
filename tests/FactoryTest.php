<?php

namespace Altapay\ApiTest;

use Altapay\Exceptions\ClassDoesNotExistsException;
use Altapay\Factory;

class FactoryTest extends AbstractTest
{
    /**
     * @return array<int, array<int, class-string>>
     */
    public function dataProvider()
    {
        $refClass = new \ReflectionClass(Factory::class);
        $constants = $refClass->getConstants();
        $output = [];
        foreach ($constants as $class) {
            $output[] = [$class];
        }
        return $output;
    }

    /**
     * @dataProvider dataProvider
     * @param class-string $class
     */
    public function test_can_create($class): void
    {
        $this->assertInstanceOf($class, Factory::create($class, $this->getAuth()));
    }

    public function test_does_not_exists(): void
    {
        $this->expectException(ClassDoesNotExistsException::class);
        Factory::create('Foo\Bar', $this->getAuth());
    }

    public function test_does_not_exists_exception_catch(): void
    {
        try {
            Factory::create('Foo\Bar', $this->getAuth());
        } catch (ClassDoesNotExistsException $e) {
            $this->assertEquals('Foo\Bar', $e->getClass());
        }
    }

}
