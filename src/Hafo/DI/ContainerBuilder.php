<?php
declare(strict_types=1);

namespace Hafo\DI;

use Hafo\DI\Container\DefaultContainer;
use Psr\Container\ContainerInterface;

final class ContainerBuilder
{
    private $factories = [];

    private $decorators = [];

    /** @var Autowiring|null */
    private $autowiring;

    public function __construct()
    {
        // register the container itself
        $this->factories = array_combine(
            [
                Container::class,
                ContainerInterface::class,
                DefaultContainer::class,
            ],
            array_fill(0, 3, function (Container $c) {
                return $c;
            })
        );
    }

    /**
     * @param callable[] $factories Array of identifier => function(Container $container)
     */
    public function addFactories(array $factories): void
    {
        foreach ($factories as $key => $value) {
            $this->factories[$key] = $value;
        }
    }

    /**
     * @param callable[][] $decorators Array of identifier => [function($service, Container $container)]
     */
    public function addDecorators(array $decorators): void
    {
        foreach ($decorators as $key => $value) {
            if (is_callable($value)) {
                $value = [$value];
            }
            if (!array_key_exists($key, $this->decorators)) {
                $this->decorators[$key] = [];
            }
            foreach ($value as $decorator) {
                $this->decorators[$key][] = $decorator;
            }
        }
    }

    public function addParameters(iterable $parameters): void
    {
        foreach ($parameters as $key => $param) {
            $this->factories[$key] = function (Container $c) use ($param) {
                return $param;
            };
        }
    }

    public function setAutowiring(Autowiring $autowiring): void
    {
        $this->autowiring = $autowiring;
    }

    public function createContainer(): DefaultContainer
    {
        return new DefaultContainer($this->factories, $this->decorators, $this->autowiring);
    }
}
