<?php

namespace HafoTest;

require __DIR__ . '/../../bootstrap.php';

use Hafo\DI\Container;
use Hafo\DI\ContainerBuilder;
use Hafo\DI\DefaultContainer;
use Interop\Container\ContainerInterface;
use Tester\TestCase;
use Tester\Assert;

class ContainerBuilderTest extends TestCase {

    private $factories;

    private $decorators;

    function __construct($factories, $decorators) {
        $this->factories = $factories;
        $this->decorators = $decorators;
    }

    function testBuilder() {
        $b = new ContainerBuilder(['test' => TRUE]);
        $b->addFactories($this->factories);
        $b->addDecorators($this->decorators);
        $container = $b->createContainer();

        Assert::same($container, $container->get(ContainerInterface::class));
        Assert::same($container, $container->get(Container::class));
        Assert::same($container, $container->get(DefaultContainer::class));

        Assert::true($container->get('test'));
        Assert::true($container->get(C::class)->isDecorated());
        Assert::true($container->get(C::class)->isDecoratedAgain());
        Assert::true($container->get(D::class)->isDecorated());

        Assert::type(D::class, $container->get(E::class));
    }

    function testMultipleDecorators() {
        $b = new ContainerBuilder;
        $b->addFactories([
            A::class => function(Container $c) {
                return new A;
            },
            C::class => function(Container $container) {
                return new C($container->get(A::class));
            }
        ]);
        $b->addDecorators([
            C::class => function(C $c, Container $container) {
                $c->decorate();
            }
        ]);
        $b->addDecorators([
            C::class => function(C $c, Container $container) {
                $c->decorateAgain();
            }
        ]);
        $container = $b->createContainer();
        Assert::true($container->get(C::class)->isDecorated());
        Assert::true($container->get(C::class)->isDecoratedAgain());
    }

}

(new ContainerBuilderTest(require __DIR__ . '/factories.php', require __DIR__ . '/decorators.php'))->run();
