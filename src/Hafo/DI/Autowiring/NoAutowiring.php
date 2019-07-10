<?php
declare(strict_types=1);

namespace Hafo\DI\Autowiring;

use Hafo\DI\Autowiring;

class NoAutowiring implements Autowiring
{
    public function createFactory($id): ?callable
    {
        return null;
    }
}
