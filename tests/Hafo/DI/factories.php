<?php

namespace HafoTest;

class A {}
class B {
    function __construct(A $a) {}
}
class C {
    private $decorated = FALSE;
    private $decoratedAgain = FALSE;
    function __construct(A $a) {}
    function decorate() {
        $this->decorated = TRUE;
    }
    function isDecorated() {
        return $this->decorated;
    }
    function decorateAgain() {
        $this->decoratedAgain = TRUE;
    }
    function isDecoratedAgain() {
        return $this->decoratedAgain;
    }
}
class D {
    private $decorated = FALSE;
    function decorate() {
        $this->decorated = TRUE;
    }
    function isDecorated() {
        return $this->decorated;
    }
}
class E {}

return [
    A::class => function(\Hafo\DI\Container $container) {
        return new A;
    },
    B::class => function(\Hafo\DI\Container $container) {
        return new B($container->get(A::class));
    },
    C::class => function(\Hafo\DI\Container $container) {
        return new C($container->get(A::class));
    },
    D::class => function(\Hafo\DI\Container $container) {
        return new D;
    },
    E::class => function(\Hafo\DI\Container $container) {
        return new E;
    },
    'config' => function(\Hafo\DI\Container $container) {
        return 'Test';
    }
];
