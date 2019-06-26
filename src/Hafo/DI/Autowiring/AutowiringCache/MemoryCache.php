<?php
declare(strict_types=1);

namespace Hafo\DI\Autowiring\AutowiringCache;

use Hafo\DI\Autowiring\AutowiringCache;

final class MemoryCache implements AutowiringCache
{
    /** @var string[][] */
    private $cache = [];

    public function loadConstructorParameters(string $id, callable $fallback): ?array
    {
        if (!array_key_exists($id, $this->cache)) {
            $this->cache[$id] = $fallback();
        }

        return $this->cache[$id];
    }
}
