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
];
