<?php

namespace HafoTest;

class A {}
class B {
    function __construct(A $a) {}
}
class C {
    private $decorated = FALSE;
    function __construct(A $a) {}
    function decorate() {
        $this->decorated = TRUE;
    }
    function isDecorated() {
        return $this->decorated;
    }
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
    'config' => function(\Hafo\DI\Container $container) {
        return 'Test';
    }
];
