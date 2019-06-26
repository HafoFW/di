<?php

namespace HafoTest;

require __DIR__ . '/../../bootstrap.php';

use Nette\Caching\Cache;
use Nette\Caching\Storages\DevNullStorage;
use Nette\Caching\Storages\MemoryStorage;
use Tester\TestCase;
use Tester\Assert;

class AutowiringTest extends TestCase {

    private $factories;

    private $decorators;

    /** @var \Hafo\DI\Container */
    private $container;

    function __construct($factories, $decorators) {
        $this->factories = $factories;
        $this->decorators = $decorators;
    }

    function setUp() {
        $cache = new Cache(new MemoryStorage);
        $this->container = new \Hafo\DI\Container\DefaultContainer($this->factories, $this->decorators, $cache);
    }

    function testContainerHas() {
        Assert::true($this->container->has(A::class));
        Assert::true($this->container->has(B::class));
        Assert::true($this->container->has(C::class));
        Assert::true($this->container->has('config'));
        Assert::true($this->container->has(Blbost::class));
        Assert::true($this->container->has(Resolvable::class));
    }

    function testContainerGet() {
        Assert::type(A::class, $this->container->get(A::class));
        Assert::type(B::class, $this->container->get(B::class));
        Assert::equal('Test', $this->container->get('config'));
        Assert::same($this->container->get(B::class), $this->container->get(B::class));
        Assert::type(Blbost::class, $this->container->get(Blbost::class));
        Assert::type(Blbost2::class, $this->container->get(Blbost2::class));
        Assert::type(Resolvable::class, $this->container->get(Resolvable::class));
        Assert::type(Something::class, $this->container->get(Something::class));
        Assert::type(NeedsSomething::class, $this->container->get(NeedsSomething::class));
    }

    function testContainerCreate() {
        Assert::type(A::class, $this->container->create(A::class));
        Assert::type(B::class, $this->container->create(B::class));
        Assert::notSame($this->container->create(B::class), $this->container->create(B::class));
        Assert::type(Blbost2::class, $this->container->create(Blbost2::class));
        Assert::type(Resolvable::class, $this->container->create(Resolvable::class));
    }

    function testAutowireParameters() {
        $obj = $this->container->create(NeedsParameters::class, 'a', 'B', ['c']);
        Assert::type(NeedsParameters::class, $obj);
        Assert::same('a', $obj->a);
        Assert::same('B', $obj->b);
        Assert::same(['c'], $obj->c);
    }

    function tearDown() {
        unset($this->container);
    }

}

(new AutowiringTest(require __DIR__ . '/factories.php', require __DIR__ . '/decorators.php'))->run();
