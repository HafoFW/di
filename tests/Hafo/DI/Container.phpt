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
        $this->container = new \Hafo\DI\Container\DefaultContainer($this->factories, $this->decorators);
    }

    function testContainerHas() {
        Assert::true($this->container->has(A::class));
        Assert::true($this->container->has(B::class));
        Assert::true($this->container->has(C::class));
        Assert::true($this->container->has('config'));
        Assert::false($this->container->has(Blbost::class));
        Assert::false($this->container->has(Resolvable::class));
        Assert::false($this->container->has(NonResolvable::class));
    }

    function testContainerGet() {
        Assert::type(A::class, $this->container->get(A::class));
        Assert::type(B::class, $this->container->get(B::class));
        Assert::equal('Test', $this->container->get('config'));
        Assert::same($this->container->get(B::class), $this->container->get(B::class));
        Assert::exception(function () {
            $this->container->get(Blbost::class);
        }, \Hafo\DI\Exception\NotFoundException::class);
        Assert::exception(function() {
            $this->container->get(Something::class);
        }, \Hafo\DI\Exception\NotFoundException::class);
    }

    function testContainerCreate() {
        Assert::type(A::class, $this->container->create(A::class));
        Assert::type(B::class, $this->container->create(B::class));
        Assert::notSame($this->container->create(B::class), $this->container->create(B::class));
        Assert::exception(function () {
            $this->container->get('Blbost');
        }, \Hafo\DI\Exception\NotFoundException::class);
    }

    function testDecorator() {
        Assert::true($this->container->get(C::class)->isDecorated());
        Assert::true($this->container->get(C::class)->isDecoratedAgain());
        Assert::true($this->container->get(D::class)->isDecorated());

        Assert::type(D::class, $this->container->get(E::class));
    }

    function tearDown() {
        unset($this->container);
    }

}

(new ContainerTest(require __DIR__ . '/factories.php', require __DIR__ . '/decorators.php'))->run();
