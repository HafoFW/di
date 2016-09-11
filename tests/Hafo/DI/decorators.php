<?php

namespace HafoTest;

return [
    C::class => [
        function(C $c, \Hafo\DI\Container $container) {
            $c->decorate();
        },
        function(C $c, \Hafo\DI\Container $container) {
            $c->decorateAgain();
        }
    ],
    D::class => function(D $d, \Hafo\DI\Container $container) {
        $d->decorate();
    },
    E::class => function(E $e, \Hafo\DI\Container $container) {
        return $container->get(D::class);
    },
];
