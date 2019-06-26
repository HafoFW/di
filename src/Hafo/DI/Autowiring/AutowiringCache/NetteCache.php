<?php
declare(strict_types=1);

namespace Hafo\DI\Autowiring\AutowiringCache;

use Hafo\DI\Autowiring\AutowiringCache;
use Nette\Caching\Cache;

final class NetteCache implements AutowiringCache
{
    /** @var Cache */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function loadConstructorParameters(string $id, callable $fallback): ?array
    {
        return $this->cache->load($id, $fallback);
    }
}
