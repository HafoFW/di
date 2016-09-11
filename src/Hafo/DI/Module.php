<?php

namespace Hafo\DI;

interface Module {

    function install(ContainerBuilder $builder);

}
