<?php

namespace Hafo\DI;

use Interop\Container\ContainerInterface;

class ContainerBuilder {

    private $factories = [];

    private $decorators = [];

    function __construct(array $params = []) {
        $this->factories = array_combine(
            [Container::class, ContainerInterface::class, DefaultContainer::class],
            array_fill(0, 3, function(Container $c) {
                return $c;
            })
        );
        foreach($params as $key => $param) {
            $this->factories[$key] = function (Container $c) use ($param) {
                return $param;
            };
        }
    }

    function addFactories($factories) {
        foreach($factories as $key => $value) {
            $this->factories[$key] = $value;
        }
        return $this;
    }

    function addDecorators($decorators) {
        foreach($decorators as $key => $value) {
            if(array_key_exists($key, $this->decorators)) {
                if(!is_array($this->decorators[$key])) {
                    $existing = $this->decorators[$key];
                    $this->decorators[$key] = [$existing];
                }
                $this->decorators[$key][] = $value;
            } else {
                $this->decorators[$key] = $value;
            }
        }
        return $this;
    }

    function createContainer() {
        return new DefaultContainer($this->factories, $this->decorators);
    }

}
