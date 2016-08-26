<?php

namespace HafoTest;

return [
    C::class => function(C $c, \Hafo\DI\Container $container) {
        $c->decorate();
    }
];
