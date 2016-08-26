<?php

namespace HafoTest;

require __DIR__ . '/../../bootstrap.php';

use Tester\TestCase;
use Tester\Assert;

class MutableContainerTest extends TestCase {

    private $factories;

    private $decorators;

    /** @var \Hafo\DI\MutableContainer */
    private $container;

    function __construct($factories, $decorators) {
        $this->factories = $factories;
        $this->decorators = $decorators;
    }

    function setUp() {
        $this->container = new \Hafo\DI\MutableContainer(new \Hafo\DI\DefaultContainer($this->factories, $this->decorators));
    }

    function testReplace() {
        $this->container->replace(A::class, new B($this->container->get(A::class)));

        // test replace
        Assert::type(B::class, $this->container->get(A::class));

        // test get/create behavior
        Assert::same($this->container->get(A::class), $this->container->get(A::class));
        Assert::notSame($this->container->create(A::class), $this->container->create(A::class));
    }

    function testAdd() {
        $this->container->add('added', function(\Hafo\DI\Container $c) {
            return new A;
        });

        // test add
        Assert::type(A::class, $this->container->get('added'));

        // test get/create behavior
        Assert::same($this->container->get('added'), $this->container->get('added'));
        Assert::notSame($this->container->create('added'), $this->container->create('added'));
    }

    function tearDown() {
        unset($this->container);
    }

}

(new MutableContainerTest(require __DIR__ . '/factories.php', require __DIR__ . '/decorators.php'))->run();
