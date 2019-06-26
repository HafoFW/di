<?php
declare(strict_types=1);

namespace Hafo\DI;

interface Autowiring
{
    /**
     * Attempts to create a factory.
     *
     * @param string $id
     * @return callable|null
     */
    public function createFactory(string $id): ?callable;
}
