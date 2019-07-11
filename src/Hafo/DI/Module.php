<?php
declare(strict_types=1);

namespace Hafo\DI;

interface Module
{
    public function install(ContainerBuilder $builder);
}
