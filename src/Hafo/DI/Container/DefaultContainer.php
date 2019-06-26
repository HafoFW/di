<?php
declare(strict_types=1);

namespace Hafo\DI\Container;

use Hafo\DI\Autowiring;
use Hafo\DI\Container;
use Hafo\DI\Exception\ContainerException;
use Hafo\DI\Exception\NotFoundException;

final class DefaultContainer implements Container
{
    /** @var callable[] */
    private $factories;

    /** @var callable[][] */
    private $decorators;

    /** @var mixed[] */
    private $services = [];

    /** @var Autowiring|null */
    private $autowiring;

    /**
     * @param callable[] $factories Array (or \ArrayAccess) of identifier => function(Container $container)
     * @param callable[][] $decorators Array (or \ArrayAccess) of identifier => [function($service, Container $container)]
     * @param Autowiring|null $autowiring
     */
    public function __construct($factories, $decorators = [], Autowiring $autowiring = null) {
        $this->factories = $factories;
        $this->decorators = $decorators;
        $this->autowiring = $autowiring;
    }

    public function get($id) {
        if(!isset($this->services[$id])) {
            if(!$this->has($id)) {
                throw new NotFoundException("Entry with id {$id} not found.");
            }
            $this->services[$id] = $this->create($id);
        }

        return $this->services[$id];
    }

    public function has($id) {
        $has = isset($this->factories[$id]);
        if(!$has && $this->autowiring) {
            $factory = $this->autowiring->createFactory($id);
            if(!$factory) {
                return false;
            }

            $this->factories[$id] = $factory;

            return true;
        }

        return $has;
    }

    public function create($id, ...$args) {
        if(!$this->has($id)) {
            throw new NotFoundException("Entry with id {$id} not found.");
        }

        try {
            $instance = $this->factories[$id]($this, ...$args);
            if(!is_object($instance)) {
                return $instance;
            }

            return $this->decorate($instance);
        } catch (\Throwable $e) {
            throw new ContainerException("Error while retrieving entry with id {$id}.", 0, $e);
        }
    }

    private function decorate($instance) {
        $decorators = array_filter($this->decorators, function($type) use ($instance) {
            return is_a($instance, $type);
        }, \ARRAY_FILTER_USE_KEY);

        foreach($decorators as $decorator) {
            $actualDecorators = $decorator;
            if(!is_array($decorator) && is_callable($decorator)) { // back compatibility
                $actualDecorators = [$decorator];
            }

            foreach($actualDecorators as $actualDecorator) {
                $ret = $actualDecorator($instance, $this);
                if($ret !== null) {
                    $instance = $ret;
                }
            }
        }

        return $instance;
    }
}
