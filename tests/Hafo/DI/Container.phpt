<?php

namespace HafoTest;

require __DIR__ . '/../../bootstrap.php';

use Tester\TestCase;
use Tester\Assert;

class ContainerTest extends TestCase {

    private $factories;

    private $decorators;

    /** @var \Hafo\DI\Container */
    private $container;

    function __construct($factories, $decorators) {
        $this->factories = $factories;
        $this->decorators = $decorators;
    }

    function setUp() {
        $this->container = new \Hafo\DI\DefaultContainer($this->factories, $this->decorators);
    }

    function testContainerHas() {
        Assert::true($this->container->has(A::class));
        Assert::true($this->container->has(B::class));
        Assert::true($this->container->has(C::class));
        Assert::true($this->container->has('config'));
        Assert::false($this->container->has('Blbost'));
    }

    function testContainerGet() {
        Assert::type(A::class, $this->container->get(A::class));
        Assert::type(B::class, $this->container->get(B::class));
        Assert::equal('Test', $this->container->get('config'));
        Assert::same($this->container->get(B::class), $this->container->get(B::class));
        Assert::exception(function () {
            $this->container->get('Blbost');
        }, \Hafo\DI\NotFoundException::class);
    }

    function testContainerCreate() {
        Assert::type(A::class, $this->container->create(A::class));
        Assert::type(B::class, $this->container->create(B::class));
        Assert::notSame($this->container->create(B::class), $this->container->create(B::class));
        Assert::exception(function () {
            $this->container->get('Blbost');
        }, \Hafo\DI\NotFoundException::class);
    }

    function testDecorator() {
        Assert::true($this->container->get(C::class)->isDecorated());
    }

    function tearDown() {
        unset($this->container);
    }

}

(new ContainerTest(require __DIR__ . '/factories.php', require __DIR__ . '/decorators.php'))->run();
