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
class Blbost {
    function __construct(A $a, B $b) {}
}
class Blbost2 {
    function __construct(Blbost $blbost, C $c) {}
}
class NonResolvable {
    function __construct(Blbost $blbost, $test) {}
}
class Resolvable {
    function __construct(Blbost2 $blbost, array $something = NULL) {}
}

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
