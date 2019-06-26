<?php
declare(strict_types=1);

namespace Hafo\DI\Autowiring;

interface AutowiringCache
{

    /**
     * Returns cached constructor parameters from a storage or calls a $fallback and stores and returns the result.
     *
     * @param string $id
     * @param callable $fallback function(): ?array
     * @return array|null
     */
    public function loadConstructorParameters(string $id, callable $fallback): ?array;

}
