<?php
declare(strict_types=1);

namespace Hafo\DI;

use Hafo\DI\Container\DefaultContainer;
use Psr\Container\ContainerInterface;

final class ContainerBuilder {

    private $factories = [];

    private $decorators = [];

    /** @var Autowiring */
    private $autowiring;

    public function __construct(array $parameters = [], Autowiring $autowiring = null) {
        // register container
        $this->factories = array_combine(
            [Container::class, ContainerInterface::class, DefaultContainer::class],
            array_fill(0, 3, function(Container $c) {
                return $c;
            })
        );

        // add parameters
        foreach($parameters as $key => $param) {
            $this->factories[$key] = function (Container $c) use ($param) {
                return $param;
            };
        }

        $this->autowiring = $autowiring;
    }

    public function addFactories($factories) {
        foreach($factories as $key => $value) {
            $this->factories[$key] = $value;
        }

        return $this;
    }

    public function addDecorators($decorators) {
        foreach($decorators as $key => $value) {
            if(array_key_exists($key, $this->decorators)) {
                $this->decorators[$key][] = $value;
            } else {
                $this->decorators[$key] = [$value];
            }
        }

        return $this;
    }

    public function createContainer(): DefaultContainer {
        return new DefaultContainer($this->factories, $this->decorators, $this->autowiring);
    }
}
