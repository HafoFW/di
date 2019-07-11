<?php
declare(strict_types=1);

namespace Hafo\DI\Autowiring;

interface AutowiringCache
{
    /**
     * Returns cached constructor parameters from a storage or calls a $fallback. If the $fallback gets called,
     * it's return value is cached in a storage and returned.
     *
     * @param string $id
     * @param callable $fallback function(): ?array
     * @return array|null
     */
    public function loadConstructorParameters(string $id, callable $fallback): ?array;
}
