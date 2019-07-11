<?php
declare(strict_types=1);

namespace Hafo\DI;

interface Autowiring
{
    public function createFactory($id): ?callable;
}
