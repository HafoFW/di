<?php
declare(strict_types=1);

namespace Tests\Unit\Hafo\DI\Container;

use Hafo\DI\Autowiring\AutowiringCache\MemoryCache;
use Hafo\DI\Autowiring\DefaultAutowiring;
use Hafo\DI\Container;
use Hafo\DI\Container\DefaultContainer;
use Hafo\DI\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

class DefaultContainerTest extends TestCase
{
    public function testGetNotFound()
    {
        self::expectException(NotFoundException::class);

        $container = new DefaultContainer();
        $container->get(\stdClass::class);
    }

    public function testGetWithAutowiring()
    {
        $container = new DefaultContainer([], [], $this->createAutowiring());

        $instance = $container->get(\stdClass::class);

        self::assertInstanceOf(\stdClass::class, $instance);
    }

    public function testCreateNotFound()
    {
        self::expectException(NotFoundException::class);

        $container = new DefaultContainer();
        $container->create(\stdClass::class);
    }

    public function testCreateWithAutowiring()
    {
        $container = new DefaultContainer([], [], $this->createAutowiring());

        $instance = $container->create(\stdClass::class);

        self::assertInstanceOf(\stdClass::class, $instance);
    }

    public function testCreateWithConstructorArguments()
    {
        $container = new DefaultContainer([], [], $this->createAutowiring());

        $instance = $container->create(\DateTimeImmutable::class, '2019-01-01', new \DateTimeZone('Asia/Baku'));

        self::assertInstanceOf(\DateTimeImmutable::class, $instance);
        self::assertEquals('2019-01-01', $instance->format('Y-m-d'));
        self::assertEquals('Asia/Baku', $instance->getTimezone()->getName());
    }

    public function testDecorators()
    {
        $decorators = [
            \stdClass::class => [
                function (\stdClass $instance, Container $container) {
                    $instance->foo = 'bar';
                },
            ],
        ];
        $container = new DefaultContainer([], $decorators, $this->createAutowiring());
        $instance = $container->get(\stdClass::class);
        self::assertInstanceOf(\stdClass::class, $instance);
        self::assertEquals('bar', $instance->foo);
    }

    public function testDecoratorsNotArrayOfArrays()
    {
        $decorators = [
            \stdClass::class => function (\stdClass $instance, Container $container) {
                $instance->foo = 'bar';
            },
        ];
        $container = new DefaultContainer([], $decorators, $this->createAutowiring());
        $instance = $container->get(\stdClass::class);
        self::assertInstanceOf(\stdClass::class, $instance);
        self::assertEquals('bar', $instance->foo);
    }

    public function testFactories()
    {
        $factories = [
            \stdClass::class => function (Container $container) {
                return (object)[
                    'foo' => 'bar',
                ];
            },
        ];
        $container = new DefaultContainer($factories, [], $this->createAutowiring());
        $instance = $container->get(\stdClass::class);
        self::assertInstanceOf(\stdClass::class, $instance);
        self::assertEquals('bar', $instance->foo);
    }

    private function createAutowiring(): DefaultAutowiring
    {
        return new DefaultAutowiring(new MemoryCache());
    }
}
