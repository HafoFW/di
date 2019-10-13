<?php
declare(strict_types=1);

namespace Hafo\DI\Autowiring;

use Hafo\DI\Autowiring;
use Hafo\DI\Container;

final class DefaultAutowiring implements Autowiring
{
    /** @var AutowiringCache */
    private $autowiringCache;

    public function __construct(AutowiringCache $autowiringCache)
    {
        $this->autowiringCache = $autowiringCache;
    }

    public function createFactory($id): ?callable
    {
        $args = $this->autowiringCache->loadConstructorParameters($id, function () use ($id) {
            return $this->resolveConstructorParameters($id);
        });

        if (!is_array($args)) {
            return null;
        }

        return function (Container $c, ...$passedArgs) use ($id, $args) {
            return (new \ReflectionClass($id))->newInstanceArgs(array_map(function ($arg) use ($c, &$passedArgs) {
                if ($arg instanceof RequiredParameter) {
                    return array_shift($passedArgs);
                }

                return class_exists($arg) || interface_exists($arg) ? $c->get($arg) : $arg;
            }, $args));
        };
    }

    private function resolveConstructorParameters($id): ?array
    {
        if (!class_exists($id)) {
            return null;
        }

        $resolved = [];

        try {
            $reflection = new \ReflectionClass($id);
        } catch (\ReflectionException $e) {
            return null;
        }

        $ctor = $reflection->getConstructor();
        if ($ctor === null) {
            return [];
        }

        foreach ($ctor->getParameters() as $param) {
            $paramClass = $param->getClass();
            if ($paramClass === null && !$param->isDefaultValueAvailable()) {
                $resolved[$param->getPosition()] = new RequiredParameter();
            } else {
                $resolved[$param->getPosition()] = $paramClass === null
                    ? $param->getDefaultValue()
                    : $paramClass->getName();
            }
        }

        return $resolved;
    }
}
