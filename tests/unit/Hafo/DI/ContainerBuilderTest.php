<?php
declare(strict_types=1);

namespace Tests\Unit\Hafo\DI;

use Hafo\DI\Autowiring\AutowiringCache\MemoryCache;
use Hafo\DI\Autowiring\DefaultAutowiring;
use Hafo\DI\Container;
use Hafo\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ContainerBuilderTest extends TestCase
{
    public function testContainerIsAutomaticallyRegistered()
    {
        $builder = new ContainerBuilder();

        $container = $builder->createContainer();

        self::assertTrue($container->has(Container::class));
        self::assertTrue($container->has(ContainerInterface::class));
        self::assertTrue($container->has(Container\DefaultContainer::class));

        self::assertEquals($container, $container->get(Container::class));
        self::assertEquals($container, $container->get(ContainerInterface::class));
        self::assertEquals($container, $container->get(Container\DefaultContainer::class));
    }

    public function testAddFactories()
    {
        $builder = new ContainerBuilder();

        $builder->addFactories([
            \stdClass::class => function (Container $container) {
                return (object)[
                    'foo' => 'bar',
                ];
            },
        ]);

        $container = $builder->createContainer();

        self::assertTrue($container->has(\stdClass::class));

        $instance = $container->get(\stdClass::class);
        self::assertInstanceOf(\stdClass::class, $instance);
        self::assertEquals('bar', $instance->foo);
    }

    public function testAddDecorators()
    {
        $builder = new ContainerBuilder();

        $builder->addFactories([
            \stdClass::class => function (Container $container) {
                return (object)[
                    'foo' => 'bar',
                ];
            },
        ]);

        $builder->addDecorators([
            \stdClass::class => [
                function (\stdClass $instance, Container $container) {
                    $instance->abc = 123;
                },
                function (\stdClass $instance, Container $container) {
                    $instance->xyz = 'zzz';
                },
            ],
        ]);

        // not necessary to be an array of arrays
        $builder->addDecorators([
            \stdClass::class => function (\stdClass $instance, Container $container) {
                $instance->hafo = 'di';
            },
        ]);

        $container = $builder->createContainer();

        self::assertTrue($container->has(\stdClass::class));

        $instance = $container->get(\stdClass::class);
        self::assertInstanceOf(\stdClass::class, $instance);
        self::assertEquals('bar', $instance->foo);
        self::assertEquals(123, $instance->abc);
        self::assertEquals('zzz', $instance->xyz);
        self::assertEquals('di', $instance->hafo);
    }

    public function testSetAutowiring()
    {
        $autowiring = new DefaultAutowiring(new MemoryCache());

        $builder = new ContainerBuilder();
        $builder->setAutowiring($autowiring);

        $container = $builder->createContainer();

        self::assertTrue($container->has(\stdClass::class));

        $instance = $container->get(\stdClass::class);
        self::assertInstanceOf(\stdClass::class, $instance);
    }

    public function testInterfaceImplementationMap()
    {
        $builder = new ContainerBuilder();
        $builder->addFactories([
            \ArrayIterator::class => function () {
                return new \ArrayIterator();
            },
        ]);
        $builder->addInterfaceImplementationMap([
            \Countable::class => \ArrayIterator::class,
        ]);

        $container = $builder->createContainer();

        self::assertTrue($container->has(\Countable::class));

        $instance = $container->get(\Countable::class);
        self::assertInstanceOf(\ArrayIterator::class, $instance);
    }
}
